<?php

/**
 * Created by PhpStorm.
 * User: Dev
 * Date: 09.11.16
 * Time: 10:24
 */
use \Freisicht\InvoiceImporter\CustomerOptionCollection;
use \Freisicht\InvoiceImporter\ProjectOptionCollection;
use \Freisicht\InvoiceImporter\CustomerOption;
use \Freisicht\InvoiceImporter\ProjectOption;
use \Monolog\Logger;

class Importer
{
    private $gsalesApi;
    private $miteApi;
    private $logger;
    private $counter = 1;
    private $customerOptions;
    private $projectOptions;
    private $start;
    private $end;

    /** @var MiteCustomerCollection */
    private $miteCustomers;
    /** @var MiteProjectCollection */
    private $miteProjects;
    /** @var GsalesCustomerCollection */
    private $gsalesCustomers;
    /** @var MiteTimeEntryCollection */
    private $miteTimeEntries;
    /** @var ImporterInvoiceEntry[] */
    private $importEntries = [];

    function __construct(GsalesApiHandler $gsalesApiHandler, MiteApiHandler $miteApiHandler, CustomerOptionCollection $customerOptionCollection, ProjectOptionCollection $projectOptionCollection, DateTime $start, DateTime $end, ImportLogger $logger)
    {
        $this->gsalesApi = $gsalesApiHandler;
        $this->miteApi = $miteApiHandler;
        $this->customerOptions = $customerOptionCollection;
        $this->projectOptions = $projectOptionCollection;
        $this->start = $start;
        $this->end = $end;
        $this->logger = $logger;
    }

    public function getApiData()
    {
        $this->logger->addInfo("Hole Daten aus der API...");

        $this->miteCustomers = $this->miteApi->getCustomers();
        $this->miteProjects = $this->miteApi->getProjects();
        $this->gsalesCustomers = $this->gsalesApi->getCustomers();
        $this->miteTimeEntries = $this->miteApi->getEntries($this->start, $this->end, $this->miteProjects->getIdsArray());

        $this->logger->addInfo("Alle benötigten API Daten wurden erfolgreich aus den APIs geholt!");
    }

    public function checkData(): bool
    {
        $this->logger->addInfo("Starte Validierungsphase...");

        foreach ($this->miteCustomers AS $miteCustomer) {
            /** @var MiteCustomer $miteCustomer */

            $customerOption = $this->customerOptions->getByMiteId($miteCustomer->getId());

            if (!$customerOption->getGsalesId()) {
                $this->logger->addError("Validierung fehlgeschlagen: Dem Mite Kunden (Id:{$miteCustomer->getId()})) wurde kein Gsales Kunde zugewiesen", [$miteCustomer, $customerOption]);
                return false;
            }

            if (!$customerOption instanceof CustomerOption) {
                $msg = "Data Check fehlgeschlagen: Konnte nicht den zugehörigen Mite Kunden (Id:{$miteCustomer->getId()}) für die Kunden Option (Id:{$customerOption->getId()}) ";
                $this->logger->addError($msg, [$miteCustomer, $customerOption]);
                return false;
            }

            $projectOption = $this->projectOptions->getByCustomerOptionId($customerOption->getId());

            if (!$projectOption instanceof ProjectOption) {
                $msg = "Data Check fehlgeschlagen: Konnte nicht den zugehörigen Mite Kunden (Id:{$miteCustomer->getId()}) für die Projekt Option (Id:{$projectOption->getId()}) ";
                $this->logger->addError($msg, [$miteCustomer, $projectOption]);
                return false;
            }

            /** @var GsalesCustomer $gsalesCustomer */
            $gsalesCustomer = $this->gsalesCustomers->getById($customerOption->getGsalesId());

            if (!$gsalesCustomer instanceof GsalesCustomer) {
                $msg = "Data Check fehlgeschlagen: Konnte nicht den zugehörigen Gsales Kunden (Id:{$gsalesCustomer->getId()}) für die Kunden Option (Id:{$customerOption->getId()}) ";
                $this->logger->addError($msg, [$gsalesCustomer, $customerOption]);
                return false;
            }

            if ($customerOption->getSkip()) {
                continue;
            }
        }

        $this->logger->addInfo("Validierungsphase erfolgreich beendet!");

        return true;
    }

    public function prepareData()
    {
        $this->logger->info("Bereite Daten vor...");

        foreach ($this->miteCustomers->getAll() AS $miteCustomer) {
            /** @var MiteCustomer $miteCustomer */

            $customerOption = $this->customerOptions->getByMiteId($miteCustomer->getId());
            $gsalesCustomer = $this->gsalesCustomers->getById($customerOption->getGsalesId());

            if ($customerOption->getSkip()) {
                continue;
            }

            $projects = $this->miteProjects->getByCustomerId($miteCustomer->getId());

            if ($projects->count() == 0) {
                continue;
            }

            $customerImportEntry = null;

            foreach ($this->miteProjects->getByCustomerId($miteCustomer->getId())->getAll() AS $miteProject) {
                /** @var MiteProject $miteProject */

                $projectOption = $this->projectOptions->getByMiteProjectId($miteProject->getId());

                if ($projectOption->getSkip()) {
                    continue;
                }

                $timeEntries = $this->miteTimeEntries->getByProjectId((int)$miteProject->getId());

                if ($timeEntries->count() == 0) {
                    continue;
                }

                $importEntry = null;
                if ($projectOption->getSeparate() || $customerImportEntry === null) {
                    $importEntry = new ImporterInvoiceEntry();
                    $importEntry->setGsalesCustomer($gsalesCustomer);
                    $importEntry->setMiteCustomer($miteCustomer);

                    if (!$projectOption->getSeparate()) {
                        $customerImportEntry = $importEntry;
                    }
                    $this->importEntries[] = $importEntry;
                } else {
                    $importEntry = $customerImportEntry;
                }

                $importEntry->addTimeEntries($timeEntries);
                $importEntry->addMiteProject($miteProject);
            }
        }

        $this->logger->info("Vorbereitungsphase erfolgreich beendet!");
    }

    public function startImport(): bool
    {
        $this->logger->info("Starte Importdurchlauf");

        foreach ($this->importEntries AS $importEntry) {
            $gsalesCustomer = $importEntry->getGsalesCustomer();
            $this->logger->info("Erstelle einen neunen Rechnungseintrag für den GsalesKunden Firma: {$gsalesCustomer->getCompany()}, Name: {$gsalesCustomer->getFirstname()} {$gsalesCustomer->getLastname()}, Id: {$gsalesCustomer->getId()}");

            try {
                $invoice = $this->gsalesApi->createInvoice($gsalesCustomer->getId());
            } catch (Exception $e) {
                $this->logger->error("Fehler aufgetreten beim Erstellen des Kunden: " . $e->getMessage());
                continue;
            }

            foreach ($importEntry->getTimeEntries()->getAll() AS $timeEntry) {
                /** @var MiteTimeEntry $timeEntry */

                /** @var MiteProject $project */
                $project = $this->miteProjects->getById($timeEntry->getProjectId());
                // calculate minutes in hours to get our "quantity" for the position
                $minutes = $timeEntry->getMinutes();
                $hours = floor($minutes / 60);
                $rest = $minutes % 60;

                if ($rest > 30) {
                    $hours++;
                } elseif ($rest > 0) {
                    $hours += 0.5;
                }

                $hourlyRate  = $timeEntry->getHourlyRate() / 100; // From Cent to Euro
                $note        = $timeEntry->getNote();
                $dateAt      = $timeEntry->getDateAt() ? (new DateTime($timeEntry->getDateAt()))->format('d.m.Y') : 'kein Datum';
                $projectName = $project->getName();
                $serviceName = $timeEntry->getServiceName();
                $posText     = "{$projectName} - {$serviceName}: {$note} ({$dateAt})";

                try {
                    $invoicePos = $this->gsalesApi->createInvoicePosition($invoice, $hours, $hourlyRate, "h", $posText, true);
                } catch (Exception $e) {
                    $this->logger->error("Fehler aufgetreten beim Erstellen des Positionseintrags: " . $e->getMessage());
                    continue;
                }
            }
            $this->logger->success("Rechnungseintrag wurde erfolgreich erstellt.");
        }


        return true;
    }
}