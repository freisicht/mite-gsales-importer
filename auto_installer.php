<?php

$filepath_app_options = get_full_filepath("config/app.json");
$filepath_app_options_dist = $filepath_app_options . ".dist";

if (file_exists($filepath_app_options)) {
    return;
}

// functions

function get_full_filepath ($projekt_file_path) {
    return __DIR__ . "/" . $projekt_file_path;
}

/**
 * @param string $filepath_from
 * @param string $filepath_to
 * @param array $search_and_replace
 */
function replace_content_from_file_and_put_to_new_file($filepath_from, $filepath_to, $search_and_replace)
{
    $file_content = file_get_contents($filepath_from);

    foreach ($search_and_replace AS $key => $value) {
        $file_content = str_replace($key, $value, $file_content);
    }

    if (file_exists($filepath_to)) {
        unlink($filepath_to);
    }

    file_put_contents($filepath_to, $file_content, 0777);
}

function perform_installation()
{
    $filepath_app_log = get_full_filepath("logs/app.log");

    // Mite
    $filepath_mite_options = get_full_filepath("config/apis/mite.json");
    $filepath_mite_options_dist = $filepath_mite_options . ".dist";

    $replace_array = [
        "YOUR_API_URL" => $_POST["mite-api-url"],
        "YOUR_API_KEY" => $_POST["mite-api-key"]
    ];

    replace_content_from_file_and_put_to_new_file($filepath_mite_options_dist, $filepath_mite_options, $replace_array);

    // Gsales
    $filepath_gsales_options = get_full_filepath("config/apis/gsales.json");
    $filepath_gsales_options_dist = $filepath_gsales_options . ".dist";

    $replace_array = [
        "YOUR_API_URL" => $_POST["gsales-api-url"],
        "YOUR_API_KEY" => $_POST["gsales-api-key"]
    ];

    replace_content_from_file_and_put_to_new_file($filepath_gsales_options_dist, $filepath_gsales_options, $replace_array);

    // Propel
    $filepath_propel_options = get_full_filepath("propel.yaml");
    $filepath_propel_options_dist = $filepath_propel_options . ".dist";

    $replace_array = [
        "YOUR_HOSTNAME" => $_POST["db-hostname"],
        "YOUR_DB_NAME"  => $_POST["db-name"],
        "YOUR_USERNAME" => $_POST["db-username"],
        "YOUR_PASSWORD" => $_POST["db-password"],
        "YOUR_LOG_PATH" => __DIR__ . "/logs/propel.log"
    ];

    replace_content_from_file_and_put_to_new_file($filepath_propel_options_dist, $filepath_propel_options, $replace_array);

    file_put_contents($filepath_app_log, "");

    $envs = "COMPOSER_HOME=" . $_POST["composer-home-path"]  . " ";

    $filepath = " " . __DIR__ . "/";

    chdir('../');

    echo shell_exec($envs . $_POST["php-command"] . $filepath . "composer.phar install");
    echo shell_exec($envs . $_POST["php-command"] . $filepath . "vendor/bin/propel convert-conf");
    echo shell_exec($envs . $_POST["php-command"] . $filepath . "vendor/bin/propel migration:diff");
    echo shell_exec($envs . $_POST["php-command"] . $filepath . "vendor/bin/propel migration:migrate");
    echo shell_exec($envs . $_POST["php-command"] . $filepath . "vendor/bin/propel model:build");
    echo shell_exec($envs . $_POST["php-command"] . $filepath . "composer.phar dump-autoload");

    chdir('web');
}

// request logic

if (isset($_POST["install"])) {
    try {
        perform_installation();
    } catch (Exception $exception) {
        echo "An Error occurred, please repeat the installation process!" . "<br>";
        echo "Error Message: " . $exception->getMessage() . "<br>";
        die;
    }

    $replace_array = [
        "YOUR_PHP_COMMAND" => $_POST["php-command"]
    ];

    replace_content_from_file_and_put_to_new_file($filepath_app_options_dist, $filepath_app_options, $replace_array);
    header('Location: /');
    die;
}

?>
<!-- user input -->

<html>
<head>
<style>
    td {
        width: 300px
    }
    input {
        width: 100%
    }
</style>
</head>
<body>
<form method="post">
    <table>
        <tr>
            <td>
                <label for="gsales-api-url">Gsales-API URL</label>
            </td>
            <td>
                <input id="gsales-api-url" name="gsales-api-url" type="text">
            </td>
        </tr>
        <tr>
            <td>
                <label for="gsales-api-key">Gsales-API Key</label>
            </td>
            <td>
                <input id="gsales-api-key" name="gsales-api-key" type="text">
            </td>
        </tr>
        <tr>
            <td>
                <label for="mite-api-url">Mite-API URL</label>
            </td>
            <td>
                <input id="mite-api-url" name="mite-api-url" type="text">
            </td>
        </tr>
        <tr>
            <td>
                <label for="mite-api-key">Mite-API Key</label>
            </td>
            <td>
                <input id="mite-api-key" name="mite-api-key" type="text">
            </td>
        </tr>
        <tr>
            <td>
                <label for="db-hostname">DB Hostname</label>
            </td>
            <td>
                <input id="db-hostname" name="db-hostname" type="text" value="localhost">
            </td>
        </tr>
        <tr>
            <td>
                <label for="db-name">DB Name</label>
            </td>
            <td>
                <input id="db-name" name="db-name" type="text" value="mite_gsales_importer">
            </td>
        </tr>
        <tr>
            <td>
                <label for="db-username">DB Username</label>
            </td>
            <td>
                <input id="db-username" name="db-username" type="text">
            </td>
        </tr>
        <tr>
            <td>
                <label for="db-password">DB Password</label>
            </td>
            <td>
                <input id="db-password" name="db-password" type="password">
            </td>
        </tr>
        <tr>
            <td>
                <label for="php-command">PHP Command (5.5+ required)</label>
            </td>
            <td>
                <input id="php-command" name="php-command" value="php" type="text">
            </td>
        </tr>
        <tr>
            <td>
                <label for="composer-home-path">Composer Homepath</label>
            </td>
            <td>
                <input id="composer-home-path" name="composer-home-path" type="text">
            </td>
        </tr>
    </table>
    <input type="submit" value="Install" name="install">
</form>
</body>
</html>

<?php
die;
?>
