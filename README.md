# REDCap Custom Project Settings Plugin

Custom Project Settings extension is a combination of a REDCap plugin and hook, designed to save configuration settings for REDCap extensions at the project level. The extension facilitates other REDCap hooks and plugins that need per-project custom settings. Once the hook is activated, it adds a section to the Project Setup tab of each project. The new section allows configuration data for REDCap extensions such as Form Render Skip Logic (FRSL) to be saved to a REDCap project's configuration.

Any REDCap extension that needs project-level configuration data can use Custom Project Settings (CPS) to fetch its configuration. The data is fetched via cps_lib included in this repository. Configuration data managed must be in JSON format, but otherwise there are no restrictions.


## Activating CPS Extension

If you are deploying the extension using UF CTS-IT's [redcap_deployment](https://github.com/ctsit/redcap_deployment) tools ([https://github.com/ctsit/redcap_deployment](https://github.com/ctsit/redcap_deployment)), you can activate these extensions with those tools as well.  If you have an environment named `vagrant` the activation would look like this:

    fab instance:vagrant activate_hook:redcap_every_page_top,cps_hook


## Deploying the CPS extension in other environments

The hook part of the extension is designed to be activated as redcap_every_page_top hook functions. The hook is dependent on a hook framework that calls _anonymous_ PHP functions such as UF CTS-IT's [Extensible REDCap Hooks](https://github.com/ctsit/extensible-redcap-hooks) ([https://github.com/ctsit/extensible-redcap-hooks](https://github.com/ctsit/extensible-redcap-hooks)).  If you are not using such a framework, the hook will need to be edited changing `return function($project_id)` to `function redcap_every_page_top($project_id)`.


## How to develop REDCap Extensions that integrate with the REDCap Custom Project Settings Plugin

For your REDCap extension to work with the Custom Project Settings extension, you will need to include a stanza in your extension much like that shown here:

    // Read configuration data from the custom_project_settings data store
    $my_extension_name = 'the_name_of_your_extension';
    require_once "../../plugins/custom_project_settings/cps_lib.php";
    $cps = new cps_lib();
    $my_settings = $cps->getAttributeData($project_id, $my_extension_name);

Use the name of your extension in the value assigned to $my_extension_name.  You must use the same name as the attribute in REDCap Custom Project Settings for the projects where you would like to use your extension. If the query fails, your extension may fail for lack of data.


## Developer Notes

When using the local test environment provided by UF CTS-IT's [redcap_deployment](https://github.com/ctsit/redcap_deployment) tools ([https://github.com/ctsit/redcap_deployment](https://github.com/ctsit/redcap_deployment)), you can use the deployment tools to configure the extension for testing in the local VM.  If you clone this repo as a child of the redcap_deployment repo, you can activate the hook and plugin for testing from the root of the redcap_deployment repo like this:

    fab instance:vagrant test_hook:redcap_every_page_top,custom_project_settings/cps_hook.php
    fab instance:vagrant test_plugin:custom_project_settings
