var customersOptionsApp = new Vue({
    el: '#customers-options',
    data: {
        dateFrom: '',
        dateTo: '',
        data: {},
        importOptions: {},
        rows: [],
        checkAllClass: '',
        useDateRange: false,
        isLoading: false
    },
    methods: {
        getMiteProjectsByCustomerId: function (miteCustomerId) {
            var projects = [];

            for (var i = 0; i < this.data.miteProjects.length; i++) {
                if (this.data.miteProjects[i].customer_id == miteCustomerId) {
                    projects.push(this.data.miteProjects[i]);
                }
            }

            return projects;
        },
        getGsalesCustomerByMiteCustomerId: function (miteCustomerId) {
            var option = this.getCustomerOptionsByMiteCustomerId(miteCustomerId);
            var gsalesCustomer = this.getGsalesCustomerById(option.GsalesId);

            if (gsalesCustomer === undefined) {
                return null;
            }

            return gsalesCustomer;
        },
        getMiteCustomerById: function (miteCustomerId) {
            var customers = this.data.miteCustomers;

            for (var i = 0; i < customers.length; i++) {
                var customer = customers[i];

                if (customer.id == miteCustomerId) {
                    return customer;
                }
            }

            return null;
        },
        getMiteProjectById: function (miteProjectId) {
            var projects = this.data.miteProjects;

            for (var i = 0; i < projects.length; i++) {
                var project = projects[i];

                if (project.id == miteProjectId) {
                    return project;
                }
            }

            return null;
        },
        getCustomerOptionsByMiteCustomerId: function (miteCustomerId) {
            for (var i = 0; i < this.data.customerOptions.length; i++) {
                if (this.data.customerOptions[i].MiteId == miteCustomerId) {
                    return this.data.customerOptions[i];
                }
            }

            return null;
        },
        getProjectOptionsByCustomerOptionId: function (customerOptionId) {
            var options = [];

            for (var i = 0; i < this.data.projectOptions.length; i++) {
                if (this.data.projectOptions[i].CustomerOptionId == customerOptionId) {
                    options.push(this.data.projectOptions[i]);
                }
            }

            return options;
        },
        getProjectOptionByMiteProjectId: function (miteProjectId) {
            for (var i = 0; i < this.data.projectOptions.length; i++) {
                if (this.data.projectOptions[i].MiteProjectId == miteProjectId) {
                    return this.data.projectOptions[i];
                }
            }

            return null;
        },
        getGsalesCustomerById: function (gsalesCustomerId) {
            for (var i = 0; i < this.data.gsalesCustomers.length; i++) {
                if (this.data.gsalesCustomers[i].id == gsalesCustomerId) {
                    return this.data.gsalesCustomers[i];
                }
            }
        },
        requestOptions: function (send = false, isImport = false) {
            var sendLink = 'options/customers/sync/';
            this.isLoading = true;

            if (send) {
                headerApp.addInfo("Speichere Daten...", 1500);
                var sendObj = {
                    customerOptions: this.data.customerOptions,
                    projectOptions: this.data.projectOptions
                };

                this.$http.post(sendLink, sendObj).then((response) => {
                    // success callback
                    if (this.areGsalesAssignsComplete()) {
                        headerApp.addSuccess("Erfolgreich gespeichert!");
                    }
                    this.isLoading = false;
                }, (response) => {
                    headerApp.addWarning("Server Fehler - Status " + response.status + ": " + response.statusText);
                    this.isLoading = false;
                });
            } else {
                headerApp.setIsLoading();
                var params = {};
                if (isImport) {
                    params = { isImport: true }
                }

                if (this.useDateRange) {
                    params.dateFrom = this.dateFrom;
                    params.dateTo = this.dateTo;
                }

                this.$http.post('options/customers/get/', params).then((response) => {
                    // success callback
                    this.data = JSON.parse(response.body);
                    console.log(this.data.timeEntries);

                    if (isImport) {
                        for(var i = 0; i < this.data.customerOptions.length; i++) {
                            var customerOption = this.data.customerOptions[i];
                            var projectOptions = this.getProjectOptionsByCustomerOptionId(customerOption.Id);

                            for(var j = 0; j < projectOptions.length; j++) {
                                var projectOption = projectOptions[j];

                                if (customerOption.Skip) {
                                    projectOption.Skip = customerOption.Skip;
                                }
                            }
                        }
                    }

                    headerApp.stopIsLoading();

                    let gsalesAssignsComplete = this.areGsalesAssignsComplete();

                    if(gsalesAssignsComplete) {
                        headerApp.addSuccess("Daten erfolgreich geladen!", 2000);
                        headerApp.addSuccess("Selektiere nun die gewünschten Importe", 2000);
                    } else if (isImport){
                        return;
                    }

                    this.updateList(isImport);
                    this.isLoading = false;
                }, (response) => {
                    headerApp.stopIsLoading();
                    headerApp.addWarning("Server Fehler - Status " + response.status + ": " + response.statusText);
                    this.isLoading = false;
                });
            }
        },
        areGsalesAssignsComplete: function () {
            for(var k = 0; k < this.data.customerOptions.length; k++) {
                if (this.data.customerOptions[k].GsalesId == null) {
                    headerApp.addWarning("Die Gsales-Kunden sind nicht vollständig zugewiesen, der Importer kann nicht durchlaufen!");
                    return false;
                }
            }

            return true;
        },
        setImporterOptions: function (e) {
            document.getElementById("importOptions").value = JSON.stringify({
                customers: this.data.customerOptions,
                projects: this.data.projectOptions,
                from: this.dateFrom,
                to: this.dateTo
            });
        },
        createRowEntry: function (indexCounter, invoiceIndexCounter, isFirstCustomerEntry, miteCustomer, miteProject, gsalesCustomer, customerOption, projectOption, hasProjects) {
            return {
                index: indexCounter,
                invoiceIndex: invoiceIndexCounter,
                firstCustomerEntry: isFirstCustomerEntry,
                miteCustomer: miteCustomer,
                miteProject: miteProject,
                gsalesCustomer: gsalesCustomer,
                customerOption: customerOption,
                projectOption: projectOption,
                hasProjects: hasProjects,
                searchKeyword: "",
                searching: false
            };
        },
        hasCustomerTimeEntries: function (customer_id) {
            let miteProjects = this.data.miteProjects;

            console.log(miteProjects);

            for (let i = 0; i < miteProjects.length; i++) {
                if (miteProjects[i].customer_id == customer_id) {
                    console.log("Customer found!");
                    if (this.hasProjectTimeEntries(miteProjects[i].id)) {
                        return true;
                    }
                }
            }

            return false;
        },
        hasProjectTimeEntries: function (project_id) {
            let timeEntries = this.data.timeEntries;

            for (let i = 0; i < timeEntries.length; i++) {
                if (timeEntries[i].project_id == project_id) {
                    return true;
                }
            }

            return false;
        },
        onClickSearchImportEntries: function () {
            this.dateFrom = $('#date-from').val();
            this.dateTo = $('#date-to').val();
            console.log(this.dateFrom, this.dateTo);
            this.requestOptions(false, true);
        },
        updateList: function (isImport = false) {
            this.rows = [];
            var mcs = this.data.miteCustomers;
            var indexCounter = 1;
            var invoiceIndexCounter = 1;
            for (var i = 0; i < mcs.length; i++) {
                var miteCustomer = mcs[i];

                if (this.useDateRange && !this.hasCustomerTimeEntries(miteCustomer.id)) {
                    console.log("hi");
                    continue;
                }

                var customerOption = this.getCustomerOptionsByMiteCustomerId(miteCustomer.id);
                var miteProjects = this.getMiteProjectsByCustomerId(miteCustomer.id);
                var gsalesCustomer = this.getGsalesCustomerById(customerOption.GsalesId);
                var projectOptions = this.getProjectOptionsByCustomerOptionId(customerOption.Id);

                var customerRows = [];

                if (projectOptions.length == 0) {
                    if (this.isImport) {
                        continue;
                    }

                    customerRows.push(this.createRowEntry(indexCounter, invoiceIndexCounter, false, miteCustomer, null, gsalesCustomer, customerOption, null, false));
                    indexCounter++;
                }

                for (var j = 0; j < miteProjects.length; j++) {
                    var miteProject = miteProjects[j];

                    if (this.useDateRange && !this.hasProjectTimeEntries(miteProject.id)) {
                        console.log("ho");
                        continue;
                    }

                    var projectOption = this.getProjectOptionByMiteProjectId(miteProject.id);
                    var row = this.createRowEntry(indexCounter, invoiceIndexCounter, false, miteCustomer, miteProject, gsalesCustomer, customerOption, projectOption, true);

                    indexCounter++;

                    if (projectOption.Separate || row.firstCustomerEntry) {
                        invoiceIndexCounter++;
                    }

                    if (projectOption.Separate || !isImport) {
                        customerRows.push(row);
                    } else {
                        customerRows.unshift(row);
                    }
                }

                customerRows[0].firstCustomerEntry = true;

                this.rows = this.rows.concat(customerRows);
            }

            var invoiceIndex = 0;
            var previousCustomerId = null;
            for (var x = 0; x < this.rows.length; x++) {
                if ((previousCustomerId !== this.rows[x].miteCustomer.id || this.rows[x].projectOption.Separate) && this.rows[x].hasProjects) {
                    previousCustomerId = this.rows[x].miteCustomer.id;
                    this.rows[x].invoiceIndex = ++invoiceIndex;
                }
            }

            if (isImport) {
                this.updateCheckAll();
            }
        },
        focusCustomerSearch: function (row_index) {
            let interval = setInterval(function () {
                let element = document.getElementById('gsales-customer-search-' + row_index);
                if (element == document.activeElement) {
                    clearInterval(interval);
                }
                console.log("hi");
                element.focus();
            }, 50);
        },
        deFocusCustomerSearch: function (row) {
            setTimeout(function () {
                row.searching = false;
            }, 150);
        },
        setGsalesCustomer: function(row, gsalesCustomer) {
            let newId = gsalesCustomer == null ? null : gsalesCustomer.id;

            row.searchKeyword = '';
            row.searching = false;

            if (row.customerOption.GsalesId != newId) {
                row.customerOption.GsalesId = gsalesCustomer == null ? null : gsalesCustomer.id;
                this.requestOptions(true)
            }
        },
        getGsalesCustomersByKeyword: function(keyword) {
            if (this.data.gsalesCustomers === undefined)
                return [];

            let gsalesCustomer = [];

            this.data.gsalesCustomers.forEach((entry) => {
                if (this.checkKeywordMatch(keyword, entry)) {
                    gsalesCustomer.push(entry);
                }
            });

            return gsalesCustomer;
        },
        getGsalesCustomerTitleById(gsalesCustomerId) {
            if (gsalesCustomerId == null) {
                return "Keinen Gsales zugewiesen!";
            }

            let title = "";
            this.data.gsalesCustomers.forEach((entry) => {
                if (entry.id == gsalesCustomerId) {
                    title = this.generateGsalesCustomerTitle(entry);
                    return false;
                }
            });

            return title;
        },
        generateGsalesCustomerTitle(gsalesCustomer) {
            return gsalesCustomer.company + " - " + gsalesCustomer.firstname + " " + gsalesCustomer.lastname;
        },
        checkKeywordMatch(keyword, gsalesCustomer) {
            let re = new RegExp(keyword, 'i');

            if (gsalesCustomer.firstname.match(re)) {
                return true;
            }

            if (gsalesCustomer.lastname.match(re)) {
                return true;
            }

            if (gsalesCustomer.company.match(re)) {
                return true;
            }

            return false;
        },
        startCheckSelects: function () {
            let interv = setInterval(() => {
                let selects = $('.gsales-select');

                console.log(selects.length);

                if (selects.length) {
                    // selects.select2();

                    clearInterval(interv);
                }
            }, 100)
        },
        getMiteCustomerSearchResults: rowId => {
            let keywords = this['mite-customer-search-' + rowId];

            console.log(keywords);

            return [
                {text: keywords},
                {text: keywords},
                {text: keywords},
            ];
        },
        setSkip: function (row) {
            if (row.projectOption.Skip && row.projectOption.Separate) {
                row.projectOption.Separate = false;
            }

            this.requestOptions(true);
        },
        setSeparate: function (row) {
            if (row.projectOption.Separate && row.projectOption.Skip) {
                row.projectOption.Skip = false;
            }

            this.requestOptions(true);
        },
        updateCheckAll: function (toggle = false) {
            if (toggle) {
                let newSkipValue = this.checkAllClass != '';

                for (let entry of this.rows) {
                    if (entry.projectOption === null)
                        continue;

                    entry.projectOption.Skip = newSkipValue;
                }
            }

            let skippedFound = false;
            let unskippedFound = false;
            for (let entry of this.rows) {
                if (entry.projectOption === null)
                    continue;

                if (entry.projectOption.Skip) {
                    skippedFound = true;
                } else {
                    unskippedFound = true;
                }
            }

            if (skippedFound && unskippedFound) {
                this.checkAllClass = 'neg';
            } else if (unskippedFound) {
                this.checkAllClass = 'pos';
            } else {
                this.checkAllClass = '';
            }
        }
    }
});
