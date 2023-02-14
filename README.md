# SWITCH LTI configuration
Moodle plugin of type ltisource that customizes LTI calls to Kaltura media provider for SWITCH users.

## Install
Clone or copy this repo into `/mod/lti/source` folder. From Moodle base folder:
```
cd mod/lti/source
git clone https://github.com/estevebadia/switch_config.git
```
## Setup
After having downloaded the plugin code, login to Moodle with administrator rights and install the plugin. Fill in the configuration settings *Kaltura host* and *LTI user_id paramater*. The options for Id are the standard user fields `user id`, `username`, `email` and `idnumber` and any custom profile field of type text. It is important that you set a valid *Kaltura host* before configuring the tools.

In order to add the external tools, go to  *Site administration* > *Plugins* > *Activity modules* > *External tool* > *Manage tools*. Tool URLs provide the basic tool configuration options, but some settings can't be configured from the tool url and we must set them manually. Follow the steps:
### Add Media gallery external tool
 - Set the Tool URL replacing your Moodle base url and click the button *Add legacy LTI* (just *Add* in Moodle 3.x): `[MOODLE BASE URL]/mod/lti/source/switch_config/tool/video_gallery.php`
 - Set the *Consumer key*, which is a number, and the *Shared secret*, which is a long sequence of digits.
 - You will get a new tool with title *Video Gallery*.
 - Click the gear icon at the top-right of the tool card to configure it further.
 - Set *Tool configuration usage*: *Show in activity chooser and as a preconfigured tool*.

### Add Video resource external tool
 - Set the Tool URL replacing your Moodle base url and click the button *Add legacy LTI* (just *Add* in Moodle 3.x): `[MOODLE BASE URL]/mod/lti/source/switch_config/tool/video_resource.php`
 - Set the *Consumer key*, which is a number, and the *Shared secret*, which is a long sequence of digits.
 - You will get a new tool with title *Video Resource*.
 - Click the gear icon at the top-right of the tool card to configure it further.
 - Set *Tool configuration usage*: *Show in activity chooser and as a preconfigured tool*.
 - If necessary, click the *Show more...* link at the end of *Tool Settings* section.
 - Check the box *Content Item Message* and copy the value from *Tool URL* to *Content selection URL*.

### Other features
 See https://knowledge.kaltura.com/help/kaltura-application-framework-kaf-lti-integration-guide for all potential Kaltura LTI features.

### Troubleshooting
 - In local development environment, be sure that Moodle can access the tool URL. In particular, check the *cURL blocked hosts list* setting in the *HTTP security* site administration section.
 - Anyway tools can be manually configured. You can use the files in Tool URLs and found the parameters Launch Url, Name, Descriptoin and Icon Url.
 - This module has been tested in Moodle 3.9 and Moodle 4.0.1, however it may work from Moodle 2.8+.

