/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

define([], function () {
    'use strict';

    /**
     * Push data
     *
     * @param dataLayer
     */
    return function (dataLayer) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push(dataLayer);
    }
});
