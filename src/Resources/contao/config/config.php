<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['privacy_afterActivation']['privacyApiBundle'] = ['huh.privacy_api.event_listener.hook_listener', 'addAppCategoriesToModel'];