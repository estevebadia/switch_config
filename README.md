# SWITCH LTI configuration
Moodle plugin of type ltisource that customizes LTI calls to Kaltura media provider for SWITCH users.

## Install
Clone or copy this repo into `/mod/lti/source` folder. From Moodle base folder:
```
cd mod/lti/source
git clone https://github.com/estevebadia/switch_config.git
```
Visit your Moodle as admin and update the database as required.
## Update
Update from git. From Moodle base folser:
```
cd mod/lti/source/switch_config
git pull
```
Visit your Moodle as admin and update the database as required.
## Setup
After having downloaded the plugin code, login to Moodle with administrator rights and install the plugin. Fill in the configuration settings *Kaltura host* and *LTI user_id paramater*. The options for Id are the standard user fields `id`, `username`, `email` and `idnumber` and any custom profile field of type text. It is important that you set a valid *Kaltura host* before configuring the tools.

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
### LTI 1.3
In order to integrate Kaltura using LTI 1.3.

 - Use the following additional settings when configuring the tool types:

 | Name | Value |
 |------|-------|
 | Public key type | `Keyset URL` |
 | Public keyset | `[KAF URL]/hosted/index/lti-advantage-key-set` |
 | Initiate login URL | `[KAF URL]/hosted/index/oidc-init` |
 | Redirection URI(s) | `[KAF URL]/hosted/index/oauth2-launch` |

 - The client_id parameter needs to be the same string in all tools, including the ones provided by the Kaltura plugin. In order to do this, go to this plugin settings page (`[MOODLE BASE URL]/admin/settings.php?section=ltisourcesettingswitch_config`) and click
 the button *Fix Client IDs*. You'll need to do that after setting new LTI 1.3 tools pointing to Kaltura.

 - See also https://knowledge.kaltura.com/help/kaltura-video-package-for-moodle-4x-installation-guide for further instructions on the Kaltura side.

### Other features
 See https://knowledge.kaltura.com/help/kaltura-application-framework-kaf-lti-integration-guide for all potential Kaltura LTI features.

### Troubleshooting
 - In local development environment, be sure that Moodle can access the tool URL. In particular, check the *cURL blocked hosts list* setting in the *HTTP security* site administration section.
 - Anyway tools can be manually configured. You can use the files in Tool URLs and found the parameters Launch Url, Name, Descriptoin and Icon Url.
 - This module has been tested in Moodle 3.9 and Moodle 4.2, however it may work from Moodle 2.8+.

