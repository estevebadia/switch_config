# SWITCH LTI configuration
Moodle plugin of type ltisource that customizes LTI calls to Kaltura media provider for SWITCH users.

## Install
Clone or copy this repo into `/mod/lti/source` folder. From Moodle base folder:
```
cd mod/lti/source
git clone git@github.com:estevebadia/switch_config.git
```
## Setup
After having downloaded the plugin code, login to Moodle with administrator rights and install the plugin. Fill in the configuration settings *Kaltura host* and *LTI user_id paramater*. It is important that you set a valid *Kaltura host* before configuring the tools.

Then add external tools for the following features. Go to  *Site administration* > *Plugins* > *Activity modules* > *External tool* > *Manage tools*.
### Add Media gallery external tool
 - Set the Tool URL replacing your Moodle base url and click the button *Add*: `[MOODLE BASE URL]/mod/lti/source/switch_config/tool/video_gallery.php`
 - Set the *Consumer key*, which is a number, and the *Shared secret*, which is a long sequence of digits.
 - You will get a new tool with title *Video Gallery*.
 - Click the gear icon at the top-right of the tool card to configure it further.
 - Set *Tool configuration usage*: *Show in activity chooser and as a preconfigured tool*.

### Add Video resource external tool
 - Set the Tool URL replacing your Moodle base url and click the button *Add*: `[MOODLE BASE URL]/mod/lti/source/switch_config/tool/video_resource.php`
 - Set the *Consumer key*, which is a number, and the *Shared secret*, which is a long sequence of digits.
 - You will get a new tool with title *Video Resource*.
 - Click the gear icon at the top-right of the tool card to configure it further.
 - Set *Tool configuration usage*: *Show in activity chooser and as a preconfigured tool*.
 - If necessary, click the *Show more...* link at the end of *Tool Settings* section.
 - Check the box *Content Item Message* and copy the value from *Tool URL* to *Content selection URL*.

 ### Other features
 See https://knowledge.kaltura.com/help/kaltura-application-framework-kaf-lti-integration-guide for all potential Kaltura LTI features.
