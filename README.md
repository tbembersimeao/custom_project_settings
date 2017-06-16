# REDCap Custom Project Settings Plugin

ToDo: Provide an overview of this plugin and hook here.

Custom Project Settings extension is a combination of a plugin and a hook designed to save settings at project level. The main reason behind developing this extension is to facilitate other hooks or plugins to have a custom settings, which can be configure at project level. Once activated it is shown in Project Setup page of each project. On click of it, the page redirects to a different page, where the configuration value can be mapped to attribute key for that project. And by using cps_lib, configuration value can be fetched and used by other hooks or plugins.


## Activating CPS Extension

If you are deploying the extension using UF CTS-IT's [redcap_deployment](https://github.com/ctsit/redcap_deployment) tools ([https://github.com/ctsit/redcap_deployment](https://github.com/ctsit/redcap_deployment)), you can activate these extension with those tools as well.  If you had an environment named `vagrant` the activation would look like this:

	fab instance:vagrant activate_plugin:redcap_custom_project_settings
    fab instance:vagrant activate_hook:redcap_every_page_top,cps_hook


## Deploying the CPS extension in other environments

The hook part of the extension is designed to be activated as redcap_every_page_top hook functions. They are dependent on a hook framework that calls _anonymous_ PHP functions such as UF CTS-IT's [Extensible REDCap Hooks](https://github.com/ctsit/extensible-redcap-hooks) ([https://github.com/ctsit/extensible-redcap-hooks](https://github.com/ctsit/extensible-redcap-hooks)).  If you are not use suc a framework, each hook will need to be edited by changing `return function($project_id)` to `function redcap_every_page_top($project_id)`.

ToDo: plugin in other environments.


## How to develop REDCap Extensions that integrate with the REDCap Custom Project Settings Plugin

The table needed for storing the settings is created if it is not already present.
For your REDCap extension to work with the REDCap Custom Project Settings Plugin, you will need to include a stanza much like that shown here:

    // Read configuration data from redcap_custom_project_settings data store
    $my_extension_name = 'the_name_of_your_extension';
    require_once "../../plugins/redcap_custom_project_settings/cps_lib.php";
    $cps = new cps_lib();
    $my_settings = $cps->getAttributeData($project_id, $my_extension_name);

## Developer Notes

When using the local test environment provided by UF CTS-IT's [redcap_deployment](https://github.com/ctsit/redcap_deployment) tools ([https://github.com/ctsit/redcap_deployment](https://github.com/ctsit/redcap_deployment)), you can use the deployment tools to configure extension for testing in the local VM.  If clone this repo as a child of the redcap_deployment repo, you can configure from the root of the redcap_deployment repo like this:

    fab instance:vagrant test_hook:redcap_every_page_top,redcap_custom_project_settings/cps_hook.php
    fab instance:vagrant test_plugin:redcap_custom_project_settings


Use the name of your extension in the value assigned to $my_extension_name.  You must use the same name as the attribute in REDCap Custom Project Settings for the projects where you would like to use your extension. If the query fails, your extension may fail for lack of data.

