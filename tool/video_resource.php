<?php
// Bootstrap Moodle.
require_once("../../../../../config.php");
global $CFG;
$kaltura_host = get_config('ltisource_switch_config', 'kaltura_host');
?><?php echo '<?xml version="1.0" encoding="utf-8" ?>'; ?>
<cartridge_basiclti_link xmlns="http://www.imsglobal.org/xsd/imslticc_v1p0" xmlns:blti="http://www.imsglobal.org/xsd/imsbasiclti_v1p0" xmlns:lticm="http://www.imsglobal.org/xsd/imslticm_v1p0" xmlns:lticp="http://www.imsglobal.org/xsd/imslticp_v1p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imslticc_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticc_v1p0.xsd
http://www.imsglobal.org/xsd/imsbasiclti_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imsbasiclti_v1p0.xsd
http://www.imsglobal.org/xsd/imslticm_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticm_v1p0.xsd
http://www.imsglobal.org/xsd/imslticp_v1p0 http://www.imsglobal.org/xsd/lti/ltiv1p0/imslticp_v1p0.xsd">
    <blti:title>Video Resource</blti:title>
    <blti:description>Add a video to the course structure.</blti:description>
    <blti:launch_url>https://<?php echo $kaltura_host; ?>/browseandembed/index/index</blti:launch_url>
    <blti:icon><?php echo $CFG->wwwroot; ?>/mod/lti/source/switch_config/tool/pix/video-resource-24.svg</blti:icon>
    <blti:vendor>
        <lticp:code>kaltura</lticp:code>
        <lticp:name>Kaltura</lticp:name>
    </blti:vendor>
</cartridge_basiclti_link>
