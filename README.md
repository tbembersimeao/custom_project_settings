# REDCap Custom Project Settings Plugin

Custom Project Settings extension is a combination of a REDCap plugin and hook, designed to save configuration settings for REDCap extensions at the project level. The extension facilitates other REDCap hooks and plugins that need per-project custom settings. Once the hook is activated, it adds a section to the Project Setup tab of each project. The new section allows configuration data for REDCap extensions such as Form Render Skip Logic (FRSL) to be saved to a REDCap project's configuration.

Any REDCap extension that needs project-level configuration data can use Custom Project Settings (CPS) to fetch its configuration. The data is fetched via cps_lib included in this repository. Configuration data managed must be in JSON format, but otherwise there are no restrictions.

## Prerequisites
- [XMan](https://github.com/ctsit/xman)

## Installation
- Download Linear Data Entry Workflow and drop `custom_project_settings` folder at `<your_redcap_docroot>/xman/extensions` directory.
- Go to **Control Center > Extensions Manager (XMan)** and enable Custom Project Settings.
- For each project you want to use this extension, go to the project home page, then access **Extensions Manager (XMan)**, and enable Custom Project Settings for that project.


## How to develop REDCap Extensions that integrate with the REDCap Custom Project Settings Plugin

For your REDCap extension to work with the Custom Project Settings extension, you will need to include a stanza in your extension much like that shown here:

    // Read configuration data from the custom_project_settings data store
    $my_extension_name = 'the_name_of_your_extension';
    require_once "../../plugins/custom_project_settings/cps_lib.php";
    $cps = new cps_lib();
    $my_settings = $cps->getAttributeData($project_id, $my_extension_name);

Use the name of your extension in the value assigned to $my_extension_name.  You must use the same name as the attribute in REDCap Custom Project Settings for the projects where you would like to use your extension. If the query fails, your extension may fail for lack of data.
