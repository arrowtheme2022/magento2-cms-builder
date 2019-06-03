/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    'jquery',
    'underscore',
    'mage/template',
    'jquery/ui',
    'mage/translate'
], function ($, _, mageTemplate) {
    'use strict';

    /**
     * Check wether the incoming string is not empty or if doesn't consist of spaces.
     *
     * @param {String} value - Value to check.
     * @returns {Boolean}
     */
    function isEmpty(value) {
        return (value.length === 0) || (value == null) || /^\s+$/.test(value);
    }

    $.widget('mage.acmSearch', {
        options: {
            autocomplete: 'off',
            minSearchLength: 2,
            responseFieldElements: 'ul li',
            selectClass: 'selected',
            template:
                '<li class="<%- data.row_class %>" id="qs-option-<%- data.index %>" role="option" data-name="<%- data.name %>" data-id="<%- data.id %>">' +
                    '<span class="qs-option-name" data-name="<%- data.name %>" data-id="<%- data.id %>">' +
                       ' <%- data.name %>' +
                    '</span>' +
                '</li>'
        },

        _create: function () {
            this.responseList = {
                indexList: null,
                selected: null
            };
            this.autoComplete = $(this.options.destinationSelector);
            this.searchForm = $(this.options.formSelector);
			this.searchInput = $(this.options.destinationText);
			this.resultHidden = $(this.options.destinationId);

            _.bindAll(this, '_onKeyDown', '_onPropertyChange');


            this.element.attr('autocomplete', this.options.autocomplete);

            this.element.on('blur', $.proxy(function () {
                setTimeout($.proxy(function () {
                    this.autoComplete.hide();
                    this._updateAriaHasPopup(false);
                }, this), 250);
            }, this));

            this.element.trigger('blur');
            this.element.on('keydown', this._onKeyDown);
            this.element.on('input propertychange', this._onPropertyChange);
        },
        /**
         * @private
         * @return {Element} The first element in the suggestion list.
         */
        _getFirstVisibleElement: function () {
            return this.responseList.indexList ? this.responseList.indexList.first() : false;
        },

        /**
         * @private
         * @return {Element} The last element in the suggestion list.
         */
        _getLastElement: function () {
            return this.responseList.indexList ? this.responseList.indexList.last() : false;
        },

        /**
         * @private
         * @param {Boolean} show Set attribute aria-haspopup to "true/false" for element.
         */
        _updateAriaHasPopup: function(show) {
            if (show) {
                this.element.attr('aria-haspopup', 'true');
            } else {
                this.element.attr('aria-haspopup', 'false');
            }
        },

        /**
         * Clears the item selected from the suggestion list and resets the suggestion list.
         * @private
         * @param {Boolean} all - Controls whether to clear the suggestion list.
         */
        _resetResponseList: function (all) {
            this.responseList.selected = null;

            if (all === true) {
                this.responseList.indexList = null;
            }
			this.resultHidden.attr('value', '');
        },
		
		_updateNameAndId: function(selectedItem){
			if((selectedItem.length) && (selectedItem.attr('data-name')!='') && (selectedItem.attr('data-id')!='')){
				this.searchInput.attr('value', selectedItem.attr('data-name'));
				this.resultHidden.attr('value', selectedItem.attr('data-id'));
			}
		},

        /**
         * Executes when keys are pressed in the search input field. Performs specific actions
         * depending on which keys are pressed.
         * @private
         * @param {Event} e - The key down event
         * @return {Boolean} Default return type for any unhandled keys
         */
        _onKeyDown: function (e) {
			
            var keyCode = e.keyCode || e.which;

            switch (keyCode) {
                case $.ui.keyCode.HOME:
                    this._getFirstVisibleElement().addClass(this.options.selectClass);
                    this.responseList.selected = this._getFirstVisibleElement();
					this._updateNameAndId(this.responseList.selected);
                    break;
                case $.ui.keyCode.END:
                    this._getLastElement().addClass(this.options.selectClass);
                    this.responseList.selected = this._getLastElement();
					this._updateNameAndId(this.responseList.selected);
                    break;
                case $.ui.keyCode.ESCAPE:
                    this._resetResponseList(true);
                    this.autoComplete.hide();
                    break;
                case $.ui.keyCode.ENTER:
					if (this.responseList.indexList) {
                        if (this.responseList.selected) {
                            this._updateNameAndId(this.responseList.selected);
                        }
                    }
					
                    break;
                case $.ui.keyCode.DOWN:
                    if (this.responseList.indexList) {
                        if (!this.responseList.selected) {
                            this._getFirstVisibleElement().addClass(this.options.selectClass);
                            this.responseList.selected = this._getFirstVisibleElement();
                        }
                        else if (!this._getLastElement().hasClass(this.options.selectClass)) {
                            this.responseList.selected = this.responseList.selected.removeClass(this.options.selectClass).next().addClass(this.options.selectClass);
                        } else {
                            this.responseList.selected.removeClass(this.options.selectClass);
                            this._getFirstVisibleElement().addClass(this.options.selectClass);
                            this.responseList.selected = this._getFirstVisibleElement();
                        }
                        this.element.val(this.responseList.selected.find('.qs-option-name').text());
                        this.element.attr('aria-activedescendant', this.responseList.selected.attr('id'));
						this._updateNameAndId(this.responseList.selected);
                    }
                    break;
                case $.ui.keyCode.UP:
                    if (this.responseList.indexList !== null) {
                        if (!this._getFirstVisibleElement().hasClass(this.options.selectClass)) {
                            this.responseList.selected = this.responseList.selected.removeClass(this.options.selectClass).prev().addClass(this.options.selectClass);
                        } else {
                            this.responseList.selected.removeClass(this.options.selectClass);
                            this._getLastElement().addClass(this.options.selectClass);
                            this.responseList.selected = this._getLastElement();
                        }
						this._updateNameAndId(this.responseList.selected);
                        this.element.val(this.responseList.selected.find('.qs-option-name').text());
                        this.element.attr('aria-activedescendant', this.responseList.selected.attr('id'));
                    }
                    break;
                default:
                    return true;
            }
        },

        /**
         * Executes when the value of the search input field changes. Executes a GET request
         * to populate a suggestion list based on entered text. Handles click (select), hover,
         * and mouseout events on the populated suggestion list dropdown.
         * @private
         */
        _onPropertyChange: function () {
            var searchField = this.element,
                clonePosition = {
                    position: 'absolute',
                    // Removed to fix display issues
                    // left: searchField.offset().left,
                    // top: searchField.offset().top + searchField.outerHeight(),
                    width: searchField.outerWidth()
                },
                source = this.options.template,
                template = mageTemplate(source),
                dropdown = $('<ul role="listbox"></ul>'),
                value = this.element.val();
			
            if (value.length >= parseInt(this.options.minSearchLength, 10)) {
				var parentDiv = this.searchInput.parent();
				parentDiv.addClass('fbuilder-form-searching');
                $.get(this.options.url, {q: value}, $.proxy(function (data) {
                    $.each(data, function(index, element) {
                        element.index = index;
                        var html = template({
                            data: element
                        });
                        dropdown.append(html);
						
                    });
                    this.responseList.indexList = this.autoComplete.html(dropdown)
                        .css(clonePosition)
                        .show()
                        .find(this.options.responseFieldElements + ':visible');

                    this._resetResponseList(false);
                    this.element.removeAttr('aria-activedescendant');
					parentDiv.removeClass('fbuilder-form-searching');

                    if (this.responseList.indexList.length) {
                        this._updateAriaHasPopup(true);
                    } else {
                        this._updateAriaHasPopup(false);
                    }

                    this.responseList.indexList
                        .on('click', function (e) {
                            this.responseList.selected = $(e.target);
							this._updateNameAndId(this.responseList.selected);
                        }.bind(this))
                        .on('mouseenter mouseleave', function (e) {
                            this.responseList.indexList.removeClass(this.options.selectClass);
                            $(e.target).addClass(this.options.selectClass);
                            this.responseList.selected = $(e.target);
                            this.element.attr('aria-activedescendant', $(e.target).attr('id'));
                        }.bind(this))
                        .on('mouseout', function (e) {
                            if (!this._getLastElement() && this._getLastElement().hasClass(this.options.selectClass)) {
                                $(e.target).removeClass(this.options.selectClass);
                                this._resetResponseList(false);
                            }
                        }.bind(this));
                }, this));
            } else {
				
                this._resetResponseList(true);
                this.autoComplete.hide();
                this._updateAriaHasPopup(false);
                this.element.removeAttr('aria-activedescendant');
            }
        }
    });

    return $.mage.acmSearch;
});
