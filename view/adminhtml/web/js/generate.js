define([
    'jquery',
    'mage/translate',
    'Mageprince_MageAI/js/model/mage-ai'
], function ($, $t, mageAIModel) {
    'use strict';

    var IMAGE_OPTION_CLASS = 'mp-mageai-include-image',
        REFRESH_INTERVAL = 1500;

    $.widget('mage.mageAigenerateWidget', {

        /**
         * Initializes event listeners for generating product descriptions.
         */
        _create: function () {
            var self = this,
                mode = window.mpMageAIDescriptionImageMode || 'unchecked';

            this._imageEnabled = mode !== 'disabled';
            this._includeImage = mode === 'checked';

            // Listen for click events on the standard generate button
            $(document).on('click', mageAIModel.options.generateBtnSelector, function () {
                var currentTarget = this;
                var type = 'full';
                if ($(this).attr('id') == mageAIModel.options.shortDescriptionFieldIdentifier) {
                    type = 'short';
                }

                var attributeData = mageAIModel.collectAttributeData();
                var image = self._imageEnabled && self._includeImage ? mageAIModel.getDescriptionImage() : null;

                mageAIModel.generateContent(attributeData, type, false, image ? image.file : '')
                    .done(function (content) {
                        if (content) {
                            mageAIModel.updateDescription(content, currentTarget);
                        }
                    })
                    .fail(function (error) {
                        console.error('Error generating content:', error);
                    });
            });

            // Listen for click events on the advanced generate button
            $(document).on('click', mageAIModel.options.advancedGenerateBtnSelector, function () {
                mageAIModel.clickAdvancedGenerateButton(this);
            });

            if (this._imageEnabled) {
                this._initImageOption();
            }
        },

        /**
         * Keeps an "Include product image" tickbox next to every Generate button, visible only
         * while the product has a photo. The gallery and Page Builder render asynchronously and
         * images can be added or removed at any time, so the tickboxes are refreshed on gallery
         * events and on a light interval.
         */
        _initImageOption: function () {
            var self = this,
                refresh = this._refreshImageOption.bind(this);

            $(document).on('change', '.' + IMAGE_OPTION_CLASS + ' input', function () {
                self._includeImage = $(this).is(':checked');
                $('.' + IMAGE_OPTION_CLASS + ' input').prop('checked', self._includeImage);
            });

            $(document).on('addItem removeItem setImageType resort', refresh);
            $('body').on('contentUpdated', refresh);
            setInterval(refresh, REFRESH_INTERVAL);
            refresh();
        },

        /**
         * Inject missing tickboxes and show or hide them depending on whether a photo exists.
         */
        _refreshImageOption: function () {
            var self = this,
                image = mageAIModel.getDescriptionImage();

            $(mageAIModel.options.advancedGenerateBtnSelector).each(function () {
                var $btn = $(this);

                if (!$btn.next('.' + IMAGE_OPTION_CLASS).length) {
                    $btn.after(self._buildImageOption());
                }
            });

            $('.' + IMAGE_OPTION_CLASS).each(function () {
                var $option = $(this),
                    $thumb = $option.find('img');

                $option.toggle(!!image);
                if (image && image.url && $thumb.attr('src') !== image.url) {
                    $thumb.attr('src', image.url).show();
                } else if (!image || !image.url) {
                    $thumb.hide();
                }
            });
        },

        /**
         * @returns {jQuery}
         */
        _buildImageOption: function () {
            var $label = $('<label/>', {
                'class': IMAGE_OPTION_CLASS,
                'title': $t('Send the product photo with the request so the AI can describe what it sees.')
            });

            $label.append($('<input/>', {type: 'checkbox'}).prop('checked', this._includeImage));
            $label.append($('<img/>', {alt: '', style: 'display:none'}));
            $label.append($('<span/>').text($t('Include product image')));

            return $label.hide();
        }
    });

    return $.mage.mageAigenerateWidget;
});
