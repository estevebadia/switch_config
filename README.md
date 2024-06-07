# SWITCH LTI configuration
Moodle plugin of type ltisource that customizes LTI calls to Kaltura media provider for SWITCH users.

Additionally, when a course is copied or backup & restored from the same site, this plugin duplicates the entries from the original Media Galleries (both course and activity galleries) to the new ones. Similarly, when a course or a Media Gallery external tool is deleted, this plugin deletes the associated categories from the Kaltura server.

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
## Setup with Kaltura plugin
There are two ways you can use Kaltura media platform from Moodle. The first one is to install the Kaltura Moodle plugin. In this case you should install the [forked version of the Kaltura plugin](https://github.com/estevebadia/kaltura_moodle_plugin). This version allows us to modify the calls to the Kaltura service by changing the user id.

Alternatively, for a lightweight integration of Kaltura, you may choose to use the core Moodle LTI activities.

## Setup with core Moodle LTI activities

After having downloaded the plugin code, login to Moodle with administrator rights and install the plugin. Fill in the configuration settings *Kaltura host* and *LTI user_id paramater*. The options for Id are the standard user fields `id`, `username`, `email` and `idnumber` and any custom profile field of type text. It is important that you set a valid *Kaltura host* before configuring the tools.

In order to add the external tools, go to  *Site administration* > *Plugins* > *Activity modules* > *External tool* > *Manage tools*. 

You can configure the tool fully manually or use the cartridge configuration file bundled with the plugin. The cartridge configuration will save you a minute but it may not work if your server can't access itself by network.

### Add Media gallery external tool

#### Cartridge configuration
 - Enter the URL `[MOODLE BASE URL]/mod/lti/source/switch_config/tool/video_gallery.php` and click the button *Add legacy LTI* (just *Add* in Moodle 3.x). 
 - You should see form asking the Consumer key and secret. Otherwise there has been an error and you should try the manual configuration or see the troubleshooting section at the end of this document.
 - Set the *Consumer key*, which usually is your kaltura partner id, a short number, and the *Shared secret*, which is a long sequence of digits.
 - You will get a new tool with title *Video Gallery*.
 - Click the gear icon at the top-right of the tool card to configure it further.
 - Set *Tool configuration usage*: *Show in activity chooser and as a preconfigured tool*.
 - Click the *Save changes* button.

####  Manual configuration
 - Click the *configure a tool manually* link. Fill in the fields, leaving others blank or defualt values:
 - Tool name: `Video Gallery`
 - Tool URL: `[KAF URL]/hosted/index/course-gallery`
 - Tool description: `Add a Kaltura Media Gallery to the course structure. Then add videos to the gallery from My Media or upload new content.`
 - Consumer key: This is your kaltura partner id, usually a 3-digit number.
 - Shared secret: This is your kaltura admin secret, a long sequence of digits.
 - Tool configuration usage: `Show in activity chooser and as a preconfigured tool`
 - Click the *show more...* link and fill both Icon URL and Secure icon URL with `[MOODLE BASE URL]/mod/lti/source/switch_config/tool/pix/video-gallery-24.svg`
 - Click the *Save changes* button.


### Add Video resource external tool

#### Cartridge configuration
 - Enter the URL `[MOODLE BASE URL]/mod/lti/source/switch_config/tool/video_resource.php` and click the button *Add legacy LTI* (just *Add* in Moodle 3.x).
 - You should see form asking the Consumer key and secret. Otherwise there has been an error and you should try the manual configuration or see the troubleshooting section at the end of this document.
 - Set the *Consumer key*, which usually is your kaltura partner id, a short number, and the *Shared secret*, which is a long sequence of digits.
 - You will get a new tool with title *Video Resource*.
 - Click the gear icon at the top-right of the tool card to configure it further.
 - Set *Tool configuration usage*: *Show in activity chooser and as a preconfigured tool*.
 - Click the *Show more...* link at the end of *Tool Settings* section (if necessary to see the next field).
 - Check the box *Content Item Message* and copy the value from *Tool URL* to *Content selection URL*.
 - Click the *Save changes* button.

#### Manual configuration
 - Click the *configure a tool manually* link. Fill in the fields, leaving others blank or defualt values:
 - Tool name: `Video Resource`
 - Tool URL: `[KAF URL]/browseandembed/index/index`
 - Tool description: `Add a video to the course structure.`
 - Consumer key: This is your kaltura partner id, usually a 3-digit number.
 - Shared secret: This is your kaltura admin secret, a long sequence of digits.
 - Tool configuration usage: `Show in activity chooser and as a preconfigured tool`
 - Click the *show more...* link and fill both Icon URL and Secure icon URL with `[MOODLE BASE URL]/mod/lti/source/switch_config/tool/pix/video-resource-24.svg`
 - Check the box *Content Item Message* and copy the value from *Tool URL* to *Content selection URL*.
 - Click the *Save changes* button.

### LTI 1.3
In order to integrate Kaltura using LTI 1.3.

 - Use the following additional settings when configuring the tool types:

 | Name | Value |
 |------|-------|
 | LTI version | `LTI 1.3` |
 | Public key type | `Keyset URL` |
 | Public keyset | `[KAF URL]/hosted/index/lti-advantage-key-set` |
 | Initiate login URL | `[KAF URL]/hosted/index/oidc-init` |
 | Redirection URI(s) | `[KAF URL]/hosted/index/oauth2-launch` |

 - The client_id parameter needs to be the same string in all tools, including the ones provided by the Kaltura plugin. In order to do this, go to this plugin settings page (`[MOODLE BASE URL]/admin/settings.php?section=ltisourcesettingswitch_config`) and click
 the button *Fix Client IDs*. You'll need to do that after setting new LTI 1.3 tools pointing to Kaltura.

 - In order for both the Kaltura plugin and External Tools with Deep Linking (Video resource) to work, the `lti13PlatformOidcAuthUrl` setting in the *Hosted* module of the KAF configuration site needs to be set to `[MOODLE BASE URL]/mod/lti/source/switch_config/auth.php`. See also https://knowledge.kaltura.com/help/kaltura-video-package-for-moodle-4x-installation-guide for further instructions on the Kaltura side.

### Other features
 See https://knowledge.kaltura.com/help/kaltura-application-framework-kaf-lti-integration-guide for all potential Kaltura LTI features.

### Troubleshooting
 - Be sure that Moodle can access the tool URL. In particular, check the *cURL blocked hosts list* setting in the *HTTP security* site administration section and be sure your server IP is not in the list while installing the tools via cartridge file. You can restore the default configuration afterwards.
 - This module has been tested in Moodle 3.9 and Moodle 4.2, however it may work from Moodle 2.8+ onwards.

