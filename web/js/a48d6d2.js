/*
Copyright 2012 Igor Vaynberg

Version: 3.3.2 Timestamp: Mon Mar 25 12:14:18 PDT 2013

This software is licensed under the Apache License, Version 2.0 (the "Apache License") or the GNU
General Public License version 2 (the "GPL License"). You may choose either license to govern your
use of this software only upon the condition that you accept all of the terms of either the Apache
License or the GPL License.

You may obtain a copy of the Apache License and the GPL License at:

    http://www.apache.org/licenses/LICENSE-2.0
    http://www.gnu.org/licenses/gpl-2.0.html

Unless required by applicable law or agreed to in writing, software distributed under the
Apache License or the GPL Licesnse is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
CONDITIONS OF ANY KIND, either express or implied. See the Apache License and the GPL License for
the specific language governing permissions and limitations under the Apache License and the GPL License.
*/
 (function ($) {
 	if(typeof $.fn.each2 == "undefined"){
 		$.fn.extend({
 			/*
			* 4-10 times faster .each replacement
			* use it carefully, as it overrides jQuery context of element on each iteration
			*/
			each2 : function (c) {
				var j = $([0]), i = -1, l = this.length;
				while (
					++i < l
					&& (j.context = j[0] = this[i])
					&& c.call(j[0], i, j) !== false //"this"=DOM, i=index, j=jQuery object
				);
				return this;
			}
 		});
 	}
})(jQuery);

(function ($, undefined) {
    "use strict";
    /*global document, window, jQuery, console */

    if (window.Select2 !== undefined) {
        return;
    }

    var KEY, AbstractSelect2, SingleSelect2, MultiSelect2, nextUid, sizer,
        lastMousePosition, $document;

    KEY = {
        TAB: 9,
        ENTER: 13,
        ESC: 27,
        SPACE: 32,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        SHIFT: 16,
        CTRL: 17,
        ALT: 18,
        PAGE_UP: 33,
        PAGE_DOWN: 34,
        HOME: 36,
        END: 35,
        BACKSPACE: 8,
        DELETE: 46,
        isArrow: function (k) {
            k = k.which ? k.which : k;
            switch (k) {
            case KEY.LEFT:
            case KEY.RIGHT:
            case KEY.UP:
            case KEY.DOWN:
                return true;
            }
            return false;
        },
        isControl: function (e) {
            var k = e.which;
            switch (k) {
            case KEY.SHIFT:
            case KEY.CTRL:
            case KEY.ALT:
                return true;
            }

            if (e.metaKey) return true;

            return false;
        },
        isFunctionKey: function (k) {
            k = k.which ? k.which : k;
            return k >= 112 && k <= 123;
        }
    };

    $document = $(document);

    nextUid=(function() { var counter=1; return function() { return counter++; }; }());

    function indexOf(value, array) {
        var i = 0, l = array.length;
        for (; i < l; i = i + 1) {
            if (equal(value, array[i])) return i;
        }
        return -1;
    }

    /**
     * Compares equality of a and b
     * @param a
     * @param b
     */
    function equal(a, b) {
        if (a === b) return true;
        if (a === undefined || b === undefined) return false;
        if (a === null || b === null) return false;
        if (a.constructor === String) return a+'' === b+''; // IE requires a+'' instead of just a
        if (b.constructor === String) return b+'' === a+''; // IE requires b+'' instead of just b
        return false;
    }

    /**
     * Splits the string into an array of values, trimming each value. An empty array is returned for nulls or empty
     * strings
     * @param string
     * @param separator
     */
    function splitVal(string, separator) {
        var val, i, l;
        if (string === null || string.length < 1) return [];
        val = string.split(separator);
        for (i = 0, l = val.length; i < l; i = i + 1) val[i] = $.trim(val[i]);
        return val;
    }

    function getSideBorderPadding(element) {
        return element.outerWidth(false) - element.width();
    }

    function installKeyUpChangeEvent(element) {
        var key="keyup-change-value";
        element.bind("keydown", function () {
            if ($.data(element, key) === undefined) {
                $.data(element, key, element.val());
            }
        });
        element.bind("keyup", function () {
            var val= $.data(element, key);
            if (val !== undefined && element.val() !== val) {
                $.removeData(element, key);
                element.trigger("keyup-change");
            }
        });
    }

    $document.bind("mousemove", function (e) {
        lastMousePosition = {x: e.pageX, y: e.pageY};
    });

    /**
     * filters mouse events so an event is fired only if the mouse moved.
     *
     * filters out mouse events that occur when mouse is stationary but
     * the elements under the pointer are scrolled.
     */
    function installFilteredMouseMove(element) {
	    element.bind("mousemove", function (e) {
            var lastpos = lastMousePosition;
            if (lastpos === undefined || lastpos.x !== e.pageX || lastpos.y !== e.pageY) {
                $(e.target).trigger("mousemove-filtered", e);
            }
        });
    }

    /**
     * Debounces a function. Returns a function that calls the original fn function only if no invocations have been made
     * within the last quietMillis milliseconds.
     *
     * @param quietMillis number of milliseconds to wait before invoking fn
     * @param fn function to be debounced
     * @param ctx object to be used as this reference within fn
     * @return debounced version of fn
     */
    function debounce(quietMillis, fn, ctx) {
        ctx = ctx || undefined;
        var timeout;
        return function () {
            var args = arguments;
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function() {
                fn.apply(ctx, args);
            }, quietMillis);
        };
    }

    /**
     * A simple implementation of a thunk
     * @param formula function used to lazily initialize the thunk
     * @return {Function}
     */
    function thunk(formula) {
        var evaluated = false,
            value;
        return function() {
            if (evaluated === false) { value = formula(); evaluated = true; }
            return value;
        };
    };

    function installDebouncedScroll(threshold, element) {
        var notify = debounce(threshold, function (e) { element.trigger("scroll-debounced", e);});
        element.bind("scroll", function (e) {
            if (indexOf(e.target, element.get()) >= 0) notify(e);
        });
    }

    function focus($el) {
        if ($el[0] === document.activeElement) return;

        /* set the focus in a 0 timeout - that way the focus is set after the processing
            of the current event has finished - which seems like the only reliable way
            to set focus */
        window.setTimeout(function() {
            var el=$el[0], pos=$el.val().length, range;

            $el.focus();

            /* make sure el received focus so we do not error out when trying to manipulate the caret.
                sometimes modals or others listeners may steal it after its set */
            if ($el.is(":visible") && el === document.activeElement) {

                /* after the focus is set move the caret to the end, necessary when we val()
                    just before setting focus */
                if(el.setSelectionRange)
                {
                    el.setSelectionRange(pos, pos);
                }
                else if (el.createTextRange) {
                    range = el.createTextRange();
                    range.collapse(false);
                    range.select();
                }
            }
        }, 0);
    }

    function killEvent(event) {
        event.preventDefault();
        event.stopPropagation();
    }
    function killEventImmediately(event) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }

    function measureTextWidth(e) {
        if (!sizer){
        	var style = e[0].currentStyle || window.getComputedStyle(e[0], null);
        	sizer = $(document.createElement("div")).css({
	            position: "absolute",
	            left: "-10000px",
	            top: "-10000px",
	            display: "none",
	            fontSize: style.fontSize,
	            fontFamily: style.fontFamily,
	            fontStyle: style.fontStyle,
	            fontWeight: style.fontWeight,
	            letterSpacing: style.letterSpacing,
	            textTransform: style.textTransform,
	            whiteSpace: "nowrap"
	        });
            sizer.attr("class","select2-sizer");
        	$("body").append(sizer);
        }
        sizer.text(e.val());
        return sizer.width();
    }

    function syncCssClasses(dest, src, adapter) {
        var classes, replacements = [], adapted;

        classes = dest.attr("class");
        if (classes) {
            classes = '' + classes; // for IE which returns object
            $(classes.split(" ")).each2(function() {
                if (this.indexOf("select2-") === 0) {
                    replacements.push(this);
                }
            });
        }
        classes = src.attr("class");
        if (classes) {
            classes = '' + classes; // for IE which returns object
            $(classes.split(" ")).each2(function() {
                if (this.indexOf("select2-") !== 0) {
                    adapted = adapter(this);
                    if (adapted) {
                        replacements.push(this);
                    }
                }
            });
        }
        dest.attr("class", replacements.join(" "));
    }


    function markMatch(text, term, markup, escapeMarkup) {
        var match=text.toUpperCase().indexOf(term.toUpperCase()),
            tl=term.length;

        if (match<0) {
            markup.push(escapeMarkup(text));
            return;
        }

        markup.push(escapeMarkup(text.substring(0, match)));
        markup.push("<span class='select2-match'>");
        markup.push(escapeMarkup(text.substring(match, match + tl)));
        markup.push("</span>");
        markup.push(escapeMarkup(text.substring(match + tl, text.length)));
    }

    /**
     * Produces an ajax-based query function
     *
     * @param options object containing configuration paramters
     * @param options.transport function that will be used to execute the ajax request. must be compatible with parameters supported by $.ajax
     * @param options.url url for the data
     * @param options.data a function(searchTerm, pageNumber, context) that should return an object containing query string parameters for the above url.
     * @param options.dataType request data type: ajax, jsonp, other datatatypes supported by jQuery's $.ajax function or the transport function if specified
     * @param options.traditional a boolean flag that should be true if you wish to use the traditional style of param serialization for the ajax request
     * @param options.quietMillis (optional) milliseconds to wait before making the ajaxRequest, helps debounce the ajax function if invoked too often
     * @param options.results a function(remoteData, pageNumber) that converts data returned form the remote request to the format expected by Select2.
     *      The expected format is an object containing the following keys:
     *      results array of objects that will be used as choices
     *      more (optional) boolean indicating whether there are more results available
     *      Example: {results:[{id:1, text:'Red'},{id:2, text:'Blue'}], more:true}
     */
    function ajax(options) {
        var timeout, // current scheduled but not yet executed request
            requestSequence = 0, // sequence used to drop out-of-order responses
            handler = null,
            quietMillis = options.quietMillis || 100,
            ajaxUrl = options.url,
            self = this;

        return function (query) {
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function () {
                requestSequence += 1; // increment the sequence
                var requestNumber = requestSequence, // this request's sequence number
                    data = options.data, // ajax data function
                    url = ajaxUrl, // ajax url string or function
                    transport = options.transport || $.ajax,
                    type = options.type || 'GET', // set type of request (GET or POST)
                    params = {};

                data = data ? data.call(self, query.term, query.page, query.context) : null;
                url = (typeof url === 'function') ? url.call(self, query.term, query.page, query.context) : url;

                if( null !== handler) { handler.abort(); }

                if (options.params) {
                    if ($.isFunction(options.params)) {
                        $.extend(params, options.params.call(self));
                    } else {
                        $.extend(params, options.params);
                    }
                }

                $.extend(params, {
                    url: url,
                    dataType: options.dataType,
                    data: data,
                    type: type,
                    cache: false,
                    success: function (data) {
                        if (requestNumber < requestSequence) {
                            return;
                        }
                        // TODO - replace query.page with query so users have access to term, page, etc.
                        var results = options.results(data, query.page);
                        query.callback(results);
                    }
                });
                handler = transport.call(self, params);
            }, quietMillis);
        };
    }

    /**
     * Produces a query function that works with a local array
     *
     * @param options object containing configuration parameters. The options parameter can either be an array or an
     * object.
     *
     * If the array form is used it is assumed that it contains objects with 'id' and 'text' keys.
     *
     * If the object form is used ti is assumed that it contains 'data' and 'text' keys. The 'data' key should contain
     * an array of objects that will be used as choices. These objects must contain at least an 'id' key. The 'text'
     * key can either be a String in which case it is expected that each element in the 'data' array has a key with the
     * value of 'text' which will be used to match choices. Alternatively, text can be a function(item) that can extract
     * the text.
     */
    function local(options) {
        var data = options, // data elements
            dataText,
            tmp,
            text = function (item) { return ""+item.text; }; // function used to retrieve the text portion of a data item that is matched against the search

		 if ($.isArray(data)) {
            tmp = data;
            data = { results: tmp };
        }

		 if ($.isFunction(data) === false) {
            tmp = data;
            data = function() { return tmp; };
        }

        var dataItem = data();
        if (dataItem.text) {
            text = dataItem.text;
            // if text is not a function we assume it to be a key name
            if (!$.isFunction(text)) {
                dataText = data.text; // we need to store this in a separate variable because in the next step data gets reset and data.text is no longer available
                text = function (item) { return item[dataText]; };
            }
        }

        return function (query) {
            var t = query.term, filtered = { results: [] }, process;
            if (t === "") {
                query.callback(data());
                return;
            }

            process = function(datum, collection) {
                var group, attr;
                datum = datum[0];
                if (datum.children) {
                    group = {};
                    for (attr in datum) {
                        if (datum.hasOwnProperty(attr)) group[attr]=datum[attr];
                    }
                    group.children=[];
                    $(datum.children).each2(function(i, childDatum) { process(childDatum, group.children); });
                    if (group.children.length || query.matcher(t, text(group), datum)) {
                        collection.push(group);
                    }
                } else {
                    if (query.matcher(t, text(datum), datum)) {
                        collection.push(datum);
                    }
                }
            };

            $(data().results).each2(function(i, datum) { process(datum, filtered.results); });
            query.callback(filtered);
        };
    }

    // TODO javadoc
    function tags(data) {
        var isFunc = $.isFunction(data);
        return function (query) {
            var t = query.term, filtered = {results: []};
            $(isFunc ? data() : data).each(function () {
                var isObject = this.text !== undefined,
                    text = isObject ? this.text : this;
                if (t === "" || query.matcher(t, text)) {
                    filtered.results.push(isObject ? this : {id: this, text: this});
                }
            });
            query.callback(filtered);
        };
    }

    /**
     * Checks if the formatter function should be used.
     *
     * Throws an error if it is not a function. Returns true if it should be used,
     * false if no formatting should be performed.
     *
     * @param formatter
     */
    function checkFormatter(formatter, formatterName) {
        if ($.isFunction(formatter)) return true;
        if (!formatter) return false;
        throw new Error("formatterName must be a function or a falsy value");
    }

    function evaluate(val) {
        return $.isFunction(val) ? val() : val;
    }

    function countResults(results) {
        var count = 0;
        $.each(results, function(i, item) {
            if (item.children) {
                count += countResults(item.children);
            } else {
                count++;
            }
        });
        return count;
    }

    /**
     * Default tokenizer. This function uses breaks the input on substring match of any string from the
     * opts.tokenSeparators array and uses opts.createSearchChoice to create the choice object. Both of those
     * two options have to be defined in order for the tokenizer to work.
     *
     * @param input text user has typed so far or pasted into the search field
     * @param selection currently selected choices
     * @param selectCallback function(choice) callback tho add the choice to selection
     * @param opts select2's opts
     * @return undefined/null to leave the current input unchanged, or a string to change the input to the returned value
     */
    function defaultTokenizer(input, selection, selectCallback, opts) {
        var original = input, // store the original so we can compare and know if we need to tell the search to update its text
            dupe = false, // check for whether a token we extracted represents a duplicate selected choice
            token, // token
            index, // position at which the separator was found
            i, l, // looping variables
            separator; // the matched separator

        if (!opts.createSearchChoice || !opts.tokenSeparators || opts.tokenSeparators.length < 1) return undefined;

        while (true) {
            index = -1;

            for (i = 0, l = opts.tokenSeparators.length; i < l; i++) {
                separator = opts.tokenSeparators[i];
                index = input.indexOf(separator);
                if (index >= 0) break;
            }

            if (index < 0) break; // did not find any token separator in the input string, bail

            token = input.substring(0, index);
            input = input.substring(index + separator.length);

            if (token.length > 0) {
                token = opts.createSearchChoice(token, selection);
                if (token !== undefined && token !== null && opts.id(token) !== undefined && opts.id(token) !== null) {
                    dupe = false;
                    for (i = 0, l = selection.length; i < l; i++) {
                        if (equal(opts.id(token), opts.id(selection[i]))) {
                            dupe = true; break;
                        }
                    }

                    if (!dupe) selectCallback(token);
                }
            }
        }

        if (original!==input) return input;
    }

    /**
     * Creates a new class
     *
     * @param superClass
     * @param methods
     */
    function clazz(SuperClass, methods) {
        var constructor = function () {};
        constructor.prototype = new SuperClass;
        constructor.prototype.constructor = constructor;
        constructor.prototype.parent = SuperClass.prototype;
        constructor.prototype = $.extend(constructor.prototype, methods);
        return constructor;
    }

    AbstractSelect2 = clazz(Object, {

        // abstract
        bind: function (func) {
            var self = this;
            return function () {
                func.apply(self, arguments);
            };
        },

        // abstract
        init: function (opts) {
            var results, search, resultsSelector = ".select2-results", mask;

            // prepare options
            this.opts = opts = this.prepareOpts(opts);

            this.id=opts.id;

            // destroy if called on an existing component
            if (opts.element.data("select2") !== undefined &&
                opts.element.data("select2") !== null) {
                this.destroy();
            }

            this.enabled=true;
            this.container = this.createContainer();

            this.containerId="s2id_"+(opts.element.attr("id") || "autogen"+nextUid());
            this.containerSelector="#"+this.containerId.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, '\\$1');
            this.container.attr("id", this.containerId);

            // cache the body so future lookups are cheap
            this.body = thunk(function() { return opts.element.closest("body"); });

            syncCssClasses(this.container, this.opts.element, this.opts.adaptContainerCssClass);

            this.container.css(evaluate(opts.containerCss));
            this.container.addClass(evaluate(opts.containerCssClass));

            this.elementTabIndex = this.opts.element.attr("tabIndex");

            // swap container for the element
            this.opts.element
                .data("select2", this)
                .addClass("select2-offscreen")
                .bind("focus.select2", function() { $(this).select2("focus"); })
                .attr("tabIndex", "-1")
                .before(this.container);
            this.container.data("select2", this);

            this.dropdown = this.container.find(".select2-drop");
            this.dropdown.addClass(evaluate(opts.dropdownCssClass));
            this.dropdown.data("select2", this);

            this.results = results = this.container.find(resultsSelector);
            this.search = search = this.container.find("input.select2-input");

            search.attr("tabIndex", this.elementTabIndex);

            this.resultsPage = 0;
            this.context = null;

            // initialize the container
            this.initContainer();

            installFilteredMouseMove(this.results);
            this.dropdown.delegate(resultsSelector, "mousemove-filtered touchstart touchmove touchend", this.bind(this.highlightUnderEvent));

            installDebouncedScroll(80, this.results);
            this.dropdown.delegate(resultsSelector, "scroll-debounced", this.bind(this.loadMoreIfNeeded));

            // if jquery.mousewheel plugin is installed we can prevent out-of-bounds scrolling of results via mousewheel
            if ($.fn.mousewheel) {
                results.mousewheel(function (e, delta, deltaX, deltaY) {
                    var top = results.scrollTop(), height;
                    if (deltaY > 0 && top - deltaY <= 0) {
                        results.scrollTop(0);
                        killEvent(e);
                    } else if (deltaY < 0 && results.get(0).scrollHeight - results.scrollTop() + deltaY <= results.height()) {
                        results.scrollTop(results.get(0).scrollHeight - results.height());
                        killEvent(e);
                    }
                });
            }

            installKeyUpChangeEvent(search);
            search.bind("keyup-change input paste", this.bind(this.updateResults));
            search.bind("focus", function () { search.addClass("select2-focused"); });
            search.bind("blur", function () { search.removeClass("select2-focused");});

            this.dropdown.delegate(resultsSelector, "mouseup", this.bind(function (e) {
                if ($(e.target).closest(".select2-result-selectable").length > 0) {
                    this.highlightUnderEvent(e);
                    this.selectHighlighted(e);
                }
            }));

            // trap all mouse events from leaving the dropdown. sometimes there may be a modal that is listening
            // for mouse events outside of itself so it can close itself. since the dropdown is now outside the select2's
            // dom it will trigger the popup close, which is not what we want
            this.dropdown.bind("click mouseup mousedown", function (e) { e.stopPropagation(); });

            if ($.isFunction(this.opts.initSelection)) {
                // initialize selection based on the current value of the source element
                this.initSelection();

                // if the user has provided a function that can set selection based on the value of the source element
                // we monitor the change event on the element and trigger it, allowing for two way synchronization
                this.monitorSource();
            }

            if (opts.element.is(":disabled") || opts.element.is("[readonly='readonly']")) this.disable();
        },

        // abstract
        destroy: function () {
            var select2 = this.opts.element.data("select2");

            if (this.propertyObserver) { delete this.propertyObserver; this.propertyObserver = null; }

            if (select2 !== undefined) {

                select2.container.remove();
                select2.dropdown.remove();
                select2.opts.element
                    .removeClass("select2-offscreen")
                    .removeData("select2")
                    .unbind(".select2")
                    .attr({"tabIndex": this.elementTabIndex})
                    .show();
            }
        },

        // abstract
        prepareOpts: function (opts) {
            var element, select, idKey, ajaxUrl;

            element = opts.element;

            if (element.get(0).tagName.toLowerCase() === "select") {
                this.select = select = opts.element;
            }

            if (select) {
                // these options are not allowed when attached to a select because they are picked up off the element itself
                $.each(["id", "multiple", "ajax", "query", "createSearchChoice", "initSelection", "data", "tags"], function () {
                    if (this in opts) {
                        throw new Error("Option '" + this + "' is not allowed for Select2 when attached to a <select> element.");
                    }
                });
            }

            opts = $.extend({}, {
                populateResults: function(container, results, query) {
                    var populate,  data, result, children, id=this.opts.id, self=this;

                    populate=function(results, container, depth) {

                        var i, l, result, selectable, disabled, compound, node, label, innerContainer, formatted;

                        results = opts.sortResults(results, container, query);

                        for (i = 0, l = results.length; i < l; i = i + 1) {

                            result=results[i];

                            disabled = (result.disabled === true);
                            selectable = (!disabled) && (id(result) !== undefined);

                            compound=result.children && result.children.length > 0;

                            node=$("<li></li>");
                            node.addClass("select2-results-dept-"+depth);
                            node.addClass("select2-result");
                            node.addClass(selectable ? "select2-result-selectable" : "select2-result-unselectable");
                            if (disabled) { node.addClass("select2-disabled"); }
                            if (compound) { node.addClass("select2-result-with-children"); }
                            node.addClass(self.opts.formatResultCssClass(result));

                            label=$(document.createElement("div"));
                            label.addClass("select2-result-label");

                            formatted=opts.formatResult(result, label, query, self.opts.escapeMarkup);
                            if (formatted!==undefined) {
                                label.html(formatted);
                            }

                            node.append(label);

                            if (compound) {

                                innerContainer=$("<ul></ul>");
                                innerContainer.addClass("select2-result-sub");
                                populate(result.children, innerContainer, depth+1);
                                node.append(innerContainer);
                            }

                            node.data("select2-data", result);
                            container.append(node);
                        }
                    };

                    populate(results, container, 0);
                }
            }, $.fn.select2.defaults, opts);

            if (typeof(opts.id) !== "function") {
                idKey = opts.id;
                opts.id = function (e) { return e[idKey]; };
            }

            if ($.isArray(opts.element.data("select2Tags"))) {
                if ("tags" in opts) {
                    throw "tags specified as both an attribute 'data-select2-tags' and in options of Select2 " + opts.element.attr("id");
                }
                opts.tags=opts.element.data("select2Tags");
            }

            if (select) {
                opts.query = this.bind(function (query) {
                    var data = { results: [], more: false },
                        term = query.term,
                        children, firstChild, process;

                    process=function(element, collection) {
                        var group;
                        if (element.is("option")) {
                            if (query.matcher(term, element.text(), element)) {
                                collection.push({id:element.attr("value"), text:element.text(), element: element.get(), css: element.attr("class"), disabled: equal(element.attr("disabled"), "disabled") });
                            }
                        } else if (element.is("optgroup")) {
                            group={text:element.attr("label"), children:[], element: element.get(), css: element.attr("class")};
                            element.children().each2(function(i, elm) { process(elm, group.children); });
                            if (group.children.length>0) {
                                collection.push(group);
                            }
                        }
                    };

                    children=element.children();

                    // ignore the placeholder option if there is one
                    if (this.getPlaceholder() !== undefined && children.length > 0) {
                        firstChild = children[0];
                        if ($(firstChild).text() === "") {
                            children=children.not(firstChild);
                        }
                    }

                    children.each2(function(i, elm) { process(elm, data.results); });

                    query.callback(data);
                });
                // this is needed because inside val() we construct choices from options and there id is hardcoded
                opts.id=function(e) { return e.id; };
                opts.formatResultCssClass = function(data) { return data.css; };
            } else {
                if (!("query" in opts)) {

                    if ("ajax" in opts) {
                        ajaxUrl = opts.element.data("ajax-url");
                        if (ajaxUrl && ajaxUrl.length > 0) {
                            opts.ajax.url = ajaxUrl;
                        }
                        opts.query = ajax.call(opts.element, opts.ajax);
                    } else if ("data" in opts) {
                        opts.query = local(opts.data);
                    } else if ("tags" in opts) {
                        opts.query = tags(opts.tags);
                        if (opts.createSearchChoice === undefined) {
                            opts.createSearchChoice = function (term) { return {id: term, text: term}; };
                        }
                        if (opts.initSelection === undefined) {
                            opts.initSelection = function (element, callback) {
                                var data = [];
                                $(splitVal(element.val(), opts.separator)).each(function () {
                                    var id = this, text = this, tags=opts.tags;
                                    if ($.isFunction(tags)) tags=tags();
                                    $(tags).each(function() { if (equal(this.id, id)) { text = this.text; return false; } });
                                    data.push({id: id, text: text});
                                });

                                callback(data);
                            };
                        }
                    }
                }
            }
            if (typeof(opts.query) !== "function") {
                throw "query function not defined for Select2 " + opts.element.attr("id");
            }

            return opts;
        },

        /**
         * Monitor the original element for changes and update select2 accordingly
         */
        // abstract
        monitorSource: function () {
            var el = this.opts.element, sync;

            el.bind("change.select2", this.bind(function (e) {
                if (this.opts.element.data("select2-change-triggered") !== true) {
                    this.initSelection();
                }
            }));

            sync = this.bind(function () {

                var enabled, readonly, self = this;

                // sync enabled state

                enabled = this.opts.element.attr("disabled") !== "disabled";
                readonly = this.opts.element.attr("readonly") === "readonly";

                enabled = enabled && !readonly;

                if (this.enabled !== enabled) {
                    if (enabled) {
                        this.enable();
                    } else {
                        this.disable();
                    }
                }


                syncCssClasses(this.container, this.opts.element, this.opts.adaptContainerCssClass);
                this.container.addClass(evaluate(this.opts.containerCssClass));

                syncCssClasses(this.dropdown, this.opts.element, this.opts.adaptDropdownCssClass);
                this.dropdown.addClass(evaluate(this.opts.dropdownCssClass));

            });

            // mozilla and IE
            el.bind("propertychange.select2 DOMAttrModified.select2", sync);
            // safari and chrome
            if (typeof WebKitMutationObserver !== "undefined") {
                if (this.propertyObserver) { delete this.propertyObserver; this.propertyObserver = null; }
                this.propertyObserver = new WebKitMutationObserver(function (mutations) {
                    mutations.forEach(sync);
                });
                this.propertyObserver.observe(el.get(0), { attributes:true, subtree:false });
            }
        },

        /**
         * Triggers the change event on the source element
         */
        // abstract
        triggerChange: function (details) {

            details = details || {};
            details= $.extend({}, details, { type: "change", val: this.val() });
            // prevents recursive triggering
            this.opts.element.data("select2-change-triggered", true);
            this.opts.element.trigger(details);
            this.opts.element.data("select2-change-triggered", false);

            // some validation frameworks ignore the change event and listen instead to keyup, click for selects
            // so here we trigger the click event manually
            this.opts.element.click();

            // ValidationEngine ignorea the change event and listens instead to blur
            // so here we trigger the blur event manually if so desired
            if (this.opts.blurOnChange)
                this.opts.element.blur();
        },

        // abstract
        enable: function() {
            if (this.enabled) return;

            this.enabled=true;
            this.container.removeClass("select2-container-disabled");
            this.opts.element.removeAttr("disabled");
        },

        // abstract
        disable: function() {
            if (!this.enabled) return;

            this.close();

            this.enabled=false;
            this.container.addClass("select2-container-disabled");
            this.opts.element.attr("disabled", "disabled");
        },

        // abstract
        opened: function () {
            return this.container.hasClass("select2-dropdown-open");
        },

        // abstract
        positionDropdown: function() {
            var offset = this.container.offset(),
                height = this.container.outerHeight(false),
                width = this.container.outerWidth(false),
                dropHeight = this.dropdown.outerHeight(false),
	            viewPortRight = $(window).scrollLeft() + $(window).width(),
                viewportBottom = $(window).scrollTop() + $(window).height(),
                dropTop = offset.top + height,
                dropLeft = offset.left,
                enoughRoomBelow = dropTop + dropHeight <= viewportBottom,
                enoughRoomAbove = (offset.top - dropHeight) >= this.body().scrollTop(),
	            dropWidth = this.dropdown.outerWidth(false),
	            enoughRoomOnRight = dropLeft + dropWidth <= viewPortRight,
                aboveNow = this.dropdown.hasClass("select2-drop-above"),
                bodyOffset,
                above,
                css;

            //console.log("below/ droptop:", dropTop, "dropHeight", dropHeight, "sum", (dropTop+dropHeight)+" viewport bottom", viewportBottom, "enough?", enoughRoomBelow);
            //console.log("above/ offset.top", offset.top, "dropHeight", dropHeight, "top", (offset.top-dropHeight), "scrollTop", this.body().scrollTop(), "enough?", enoughRoomAbove);

            // fix positioning when body has an offset and is not position: static

            if (this.body().css('position') !== 'static') {
                bodyOffset = this.body().offset();
                dropTop -= bodyOffset.top;
                dropLeft -= bodyOffset.left;
            }

            // always prefer the current above/below alignment, unless there is not enough room

            if (aboveNow) {
                above = true;
                if (!enoughRoomAbove && enoughRoomBelow) above = false;
            } else {
                above = false;
                if (!enoughRoomBelow && enoughRoomAbove) above = true;
            }

            if (!enoughRoomOnRight) {
               dropLeft = offset.left + width - dropWidth;
            }

            if (above) {
                dropTop = offset.top - dropHeight;
                this.container.addClass("select2-drop-above");
                this.dropdown.addClass("select2-drop-above");
            }
            else {
                this.container.removeClass("select2-drop-above");
                this.dropdown.removeClass("select2-drop-above");
            }

            css = $.extend({
                top: dropTop,
                left: dropLeft,
                width: width
            }, evaluate(this.opts.dropdownCss));

            this.dropdown.css(css);
        },

        // abstract
        shouldOpen: function() {
            var event;

            if (this.opened()) return false;

            event = $.Event("opening");
            this.opts.element.trigger(event);
            return !event.isDefaultPrevented();
        },

        // abstract
        clearDropdownAlignmentPreference: function() {
            // clear the classes used to figure out the preference of where the dropdown should be opened
            this.container.removeClass("select2-drop-above");
            this.dropdown.removeClass("select2-drop-above");
        },

        /**
         * Opens the dropdown
         *
         * @return {Boolean} whether or not dropdown was opened. This method will return false if, for example,
         * the dropdown is already open, or if the 'open' event listener on the element called preventDefault().
         */
        // abstract
        open: function () {

            if (!this.shouldOpen()) return false;

            window.setTimeout(this.bind(this.opening), 1);

            return true;
        },

        /**
         * Performs the opening of the dropdown
         */
        // abstract
        opening: function() {
            var cid = this.containerId,
                scroll = "scroll." + cid,
                resize = "resize."+cid,
                orient = "orientationchange."+cid,
                mask;

            this.clearDropdownAlignmentPreference();

            this.container.addClass("select2-dropdown-open").addClass("select2-container-active");


            if(this.dropdown[0] !== this.body().children().last()[0]) {
                this.dropdown.detach().appendTo(this.body());
            }

            this.updateResults(true);

            // create the dropdown mask if doesnt already exist
            mask = $("#select2-drop-mask");
            if (mask.length == 0) {
                mask = $(document.createElement("div"));
                mask.attr("id","select2-drop-mask").attr("class","select2-drop-mask");
                mask.hide();
                mask.appendTo(this.body());
                mask.bind("mousedown touchstart", function (e) {
                    var dropdown = $("#select2-drop"), self;
                    if (dropdown.length > 0) {
                        self=dropdown.data("select2");
                        if (self.opts.selectOnBlur) {
                            self.selectHighlighted({noFocus: true});
                        }
                        self.close();
                    }
                });
            }

            // ensure the mask is always right before the dropdown
            if (this.dropdown.prev()[0] !== mask[0]) {
                this.dropdown.before(mask);
            }

            // move the global id to the correct dropdown
            $("#select2-drop").removeAttr("id");
            this.dropdown.attr("id", "select2-drop");

            // show the elements
            mask.css(_makeMaskCss());
            mask.show();
            this.dropdown.show();
            this.positionDropdown();

            this.dropdown.addClass("select2-drop-active");
            this.ensureHighlightVisible();

            // attach listeners to events that can change the position of the container and thus require
            // the position of the dropdown to be updated as well so it does not come unglued from the container
            var that = this;
            this.container.parents().add(window).each(function () {
                $(this).bind(resize+" "+scroll+" "+orient, function (e) {
                    $("#select2-drop-mask").css(_makeMaskCss());
                    that.positionDropdown();
                });
            });

            this.focusSearch();

            function _makeMaskCss() {
                return {
                    width  : Math.max(document.documentElement.scrollWidth,  $(window).width()),
                    height : Math.max(document.documentElement.scrollHeight, $(window).height())
                }
            }
        },

        // abstract
        close: function () {
            if (!this.opened()) return;

            var cid = this.containerId,
                scroll = "scroll." + cid,
                resize = "resize."+cid,
                orient = "orientationchange."+cid;

            // unbind event listeners
            this.container.parents().add(window).each(function () { $(this).unbind(scroll).unbind(resize).unbind(orient); });

            this.clearDropdownAlignmentPreference();

            $("#select2-drop-mask").hide();
            this.dropdown.removeAttr("id"); // only the active dropdown has the select2-drop id
            this.dropdown.hide();
            this.container.removeClass("select2-dropdown-open");
            this.results.empty();
            this.clearSearch();
            this.search.removeClass("select2-active");
            this.opts.element.trigger($.Event("close"));
        },

        // abstract
        clearSearch: function () {

        },

        //abstract
        getMaximumSelectionSize: function() {
            return evaluate(this.opts.maximumSelectionSize);
        },

        // abstract
        ensureHighlightVisible: function () {
            var results = this.results, children, index, child, hb, rb, y, more;

            index = this.highlight();

            if (index < 0) return;

            if (index == 0) {

                // if the first element is highlighted scroll all the way to the top,
                // that way any unselectable headers above it will also be scrolled
                // into view

                results.scrollTop(0);
                return;
            }

            children = this.findHighlightableChoices();

            child = $(children[index]);

            hb = child.offset().top + child.outerHeight(true);

            // if this is the last child lets also make sure select2-more-results is visible
            if (index === children.length - 1) {
                more = results.find("li.select2-more-results");
                if (more.length > 0) {
                    hb = more.offset().top + more.outerHeight(true);
                }
            }

            rb = results.offset().top + results.outerHeight(true);
            if (hb > rb) {
                results.scrollTop(results.scrollTop() + (hb - rb));
            }
            y = child.offset().top - results.offset().top;

            // make sure the top of the element is visible
            if (y < 0 && child.css('display') != 'none' ) {
                results.scrollTop(results.scrollTop() + y); // y is negative
            }
        },

        // abstract
        findHighlightableChoices: function() {
            var h=this.results.find(".select2-result-selectable:not(.select2-selected):not(.select2-disabled)");
            return this.results.find(".select2-result-selectable:not(.select2-selected):not(.select2-disabled)");
        },

        // abstract
        moveHighlight: function (delta) {
            var choices = this.findHighlightableChoices(),
                index = this.highlight();

            while (index > -1 && index < choices.length) {
                index += delta;
                var choice = $(choices[index]);
                if (choice.hasClass("select2-result-selectable") && !choice.hasClass("select2-disabled") && !choice.hasClass("select2-selected")) {
                    this.highlight(index);
                    break;
                }
            }
        },

        // abstract
        highlight: function (index) {
            var choices = this.findHighlightableChoices(),
                choice,
                data;

            if (arguments.length === 0) {
                return indexOf(choices.filter(".select2-highlighted")[0], choices.get());
            }

            if (index >= choices.length) index = choices.length - 1;
            if (index < 0) index = 0;

            this.results.find(".select2-highlighted").removeClass("select2-highlighted");

            choice = $(choices[index]);
            choice.addClass("select2-highlighted");

            this.ensureHighlightVisible();

            data = choice.data("select2-data");
            if (data) {
                this.opts.element.trigger({ type: "highlight", val: this.id(data), choice: data });
            }
        },

        // abstract
        countSelectableResults: function() {
            return this.findHighlightableChoices().length;
        },

        // abstract
        highlightUnderEvent: function (event) {
            var el = $(event.target).closest(".select2-result-selectable");
            if (el.length > 0 && !el.is(".select2-highlighted")) {
        		var choices = this.findHighlightableChoices();
                this.highlight(choices.index(el));
            } else if (el.length == 0) {
                // if we are over an unselectable item remove al highlights
                this.results.find(".select2-highlighted").removeClass("select2-highlighted");
            }
        },

        // abstract
        loadMoreIfNeeded: function () {
            var results = this.results,
                more = results.find("li.select2-more-results"),
                below, // pixels the element is below the scroll fold, below==0 is when the element is starting to be visible
                offset = -1, // index of first element without data
                page = this.resultsPage + 1,
                self=this,
                term=this.search.val(),
                context=this.context;

            if (more.length === 0) return;
            below = more.offset().top - results.offset().top - results.height();

            if (below <= this.opts.loadMorePadding) {
                more.addClass("select2-active");
                this.opts.query({
                        element: this.opts.element,
                        term: term,
                        page: page,
                        context: context,
                        matcher: this.opts.matcher,
                        callback: this.bind(function (data) {

                    // ignore a response if the select2 has been closed before it was received
                    if (!self.opened()) return;


                    self.opts.populateResults.call(this, results, data.results, {term: term, page: page, context:context});
                    self.postprocessResults(data, false, false);

                    if (data.more===true) {
                        more.detach().appendTo(results).text(self.opts.formatLoadMore(page+1));
                        window.setTimeout(function() { self.loadMoreIfNeeded(); }, 10);
                    } else {
                        more.remove();
                    }
                    self.positionDropdown();
                    self.resultsPage = page;
                    self.context = data.context;
                })});
            }
        },

        /**
         * Default tokenizer function which does nothing
         */
        tokenize: function() {

        },

        /**
         * @param initial whether or not this is the call to this method right after the dropdown has been opened
         */
        // abstract
        updateResults: function (initial) {
            var search = this.search,
                results = this.results,
                opts = this.opts,
                data,
                self = this,
                input,
                term = search.val(),
                lastTerm=$.data(this.container, "select2-last-term");

            // prevent duplicate queries against the same term
            if (initial !== true && lastTerm && equal(term, lastTerm)) return;

            $.data(this.container, "select2-last-term", term);

            // if the search is currently hidden we do not alter the results
            if (initial !== true && (this.showSearchInput === false || !this.opened())) {
                return;
            }

            function postRender() {
                results.scrollTop(0);
                search.removeClass("select2-active");
                self.positionDropdown();
            }

            function render(html) {
                results.html(html);
                postRender();
            }

            var maxSelSize = this.getMaximumSelectionSize();
            if (maxSelSize >=1) {
                data = this.data();
                if ($.isArray(data) && data.length >= maxSelSize && checkFormatter(opts.formatSelectionTooBig, "formatSelectionTooBig")) {
            	    render("<li class='select2-selection-limit'>" + opts.formatSelectionTooBig(maxSelSize) + "</li>");
            	    return;
                }
            }

            if (search.val().length < opts.minimumInputLength) {
                if (checkFormatter(opts.formatInputTooShort, "formatInputTooShort")) {
                    render("<li class='select2-no-results'>" + opts.formatInputTooShort(search.val(), opts.minimumInputLength) + "</li>");
                } else {
                    render("");
                }
                return;
            }

            if (opts.maximumInputLength && search.val().length > opts.maximumInputLength) {
                if (checkFormatter(opts.formatInputTooLong, "formatInputTooLong")) {
                    render("<li class='select2-no-results'>" + opts.formatInputTooLong(search.val(), opts.maximumInputLength) + "</li>");
                } else {
                    render("");
                }
                return;
            }

            if (opts.formatSearching && this.findHighlightableChoices().length === 0) {
                render("<li class='select2-searching'>" + opts.formatSearching() + "</li>");
            }

            search.addClass("select2-active");

            // give the tokenizer a chance to pre-process the input
            input = this.tokenize();
            if (input != undefined && input != null) {
                search.val(input);
            }

            this.resultsPage = 1;

            opts.query({
                element: opts.element,
                    term: search.val(),
                    page: this.resultsPage,
                    context: null,
                    matcher: opts.matcher,
                    callback: this.bind(function (data) {
                var def; // default choice

                // ignore a response if the select2 has been closed before it was received
                if (!this.opened()) {
                    this.search.removeClass("select2-active");
                    return;
                }

                // save context, if any
                this.context = (data.context===undefined) ? null : data.context;
                // create a default choice and prepend it to the list
                if (this.opts.createSearchChoice && search.val() !== "") {
                    def = this.opts.createSearchChoice.call(null, search.val(), data.results);
                    if (def !== undefined && def !== null && self.id(def) !== undefined && self.id(def) !== null) {
                        if ($(data.results).filter(
                            function () {
                                return equal(self.id(this), self.id(def));
                            }).length === 0) {
                            data.results.unshift(def);
                        }
                    }
                }

                if (data.results.length === 0 && checkFormatter(opts.formatNoMatches, "formatNoMatches")) {
                    render("<li class='select2-no-results'>" + opts.formatNoMatches(search.val()) + "</li>");
                    return;
                }

                results.empty();
                self.opts.populateResults.call(this, results, data.results, {term: search.val(), page: this.resultsPage, context:null});

                if (data.more === true && checkFormatter(opts.formatLoadMore, "formatLoadMore")) {
                    results.append("<li class='select2-more-results'>" + self.opts.escapeMarkup(opts.formatLoadMore(this.resultsPage)) + "</li>");
                    window.setTimeout(function() { self.loadMoreIfNeeded(); }, 10);
                }

                this.postprocessResults(data, initial);

                postRender();

                this.opts.element.trigger({ type: "loaded", data:data });
            })});
        },

        // abstract
        cancel: function () {
            this.close();
        },

        // abstract
        blur: function () {
            // if selectOnBlur == true, select the currently highlighted option
            if (this.opts.selectOnBlur)
                this.selectHighlighted({noFocus: true});

            this.close();
            this.container.removeClass("select2-container-active");
            // synonymous to .is(':focus'), which is available in jquery >= 1.6
            if (this.search[0] === document.activeElement) { this.search.blur(); }
            this.clearSearch();
            this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus");
        },

        // abstract
        focusSearch: function () {
            focus(this.search);
        },

        // abstract
        selectHighlighted: function (options) {
            var index=this.highlight(),
                highlighted=this.results.find(".select2-highlighted"),
                data = highlighted.closest('.select2-result').data("select2-data");

            if (data) {
                this.highlight(index);
                this.onSelect(data, options);
            }
        },

        // abstract
        getPlaceholder: function () {
            return this.opts.element.attr("placeholder") ||
                this.opts.element.attr("data-placeholder") || // jquery 1.4 compat
                this.opts.element.data("placeholder") ||
                this.opts.placeholder;
        },

        /**
         * Get the desired width for the container element.  This is
         * derived first from option `width` passed to select2, then
         * the inline 'style' on the original element, and finally
         * falls back to the jQuery calculated element width.
         */
        // abstract
        initContainerWidth: function () {
            function resolveContainerWidth() {
                var style, attrs, matches, i, l;

                if (this.opts.width === "off") {
                    return null;
                } else if (this.opts.width === "element"){
                    return this.opts.element.outerWidth(false) === 0 ? 'auto' : this.opts.element.outerWidth(false) + 'px';
                } else if (this.opts.width === "copy" || this.opts.width === "resolve") {
                    // check if there is inline style on the element that contains width
                    style = this.opts.element.attr('style');
                    if (style !== undefined) {
                        attrs = style.split(';');
                        for (i = 0, l = attrs.length; i < l; i = i + 1) {
                            matches = attrs[i].replace(/\s/g, '')
                                .match(/width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/);
                            if (matches !== null && matches.length >= 1)
                                return matches[1];
                        }
                    }

                    if (this.opts.width === "resolve") {
                        // next check if css('width') can resolve a width that is percent based, this is sometimes possible
                        // when attached to input type=hidden or elements hidden via css
                        style = this.opts.element.css('width');
                        if (style.indexOf("%") > 0) return style;

                        // finally, fallback on the calculated width of the element
                        return (this.opts.element.outerWidth(false) === 0 ? 'auto' : this.opts.element.outerWidth(false) + 'px');
                    }

                    return null;
                } else if ($.isFunction(this.opts.width)) {
                    return this.opts.width();
                } else {
                    return this.opts.width;
               }
            };

            var width = resolveContainerWidth.call(this);
            if (width !== null) {
                this.container.css("width", width);
            }
        }
    });

    SingleSelect2 = clazz(AbstractSelect2, {

        // single

		createContainer: function () {
            var container = $(document.createElement("div")).attr({
                "class": "select2-container"
            }).html([
                "<a href='javascript:void(0)' onclick='return false;' class='select2-choice' tabindex='-1'>",
                "   <span></span><abbr class='select2-search-choice-close' style='display:none;'></abbr>",
                "   <div><b></b></div>" ,
                "</a>",
                "<input class='select2-focusser select2-offscreen' type='text'/>",
                "<div class='select2-drop' style='display:none'>" ,
                "   <div class='select2-search'>" ,
                "       <input type='text' autocomplete='off' class='select2-input'/>" ,
                "   </div>" ,
                "   <ul class='select2-results'>" ,
                "   </ul>" ,
                "</div>"].join(""));
            return container;
        },

        // single
        disable: function() {
            if (!this.enabled) return;

            this.parent.disable.apply(this, arguments);

            this.focusser.attr("disabled", "disabled");
        },

        // single
        enable: function() {
            if (this.enabled) return;

            this.parent.enable.apply(this, arguments);

            this.focusser.removeAttr("disabled");
        },

        // single
        opening: function () {
            this.parent.opening.apply(this, arguments);
            this.focusser.attr("disabled", "disabled");

            this.opts.element.trigger($.Event("open"));
        },

        // single
        close: function () {
            if (!this.opened()) return;
            this.parent.close.apply(this, arguments);
            this.focusser.removeAttr("disabled");
            focus(this.focusser);
        },

        // single
        focus: function () {
            if (this.opened()) {
                this.close();
            } else {
                this.focusser.removeAttr("disabled");
                this.focusser.focus();
            }
        },

        // single
        isFocused: function () {
            return this.container.hasClass("select2-container-active");
        },

        // single
        cancel: function () {
            this.parent.cancel.apply(this, arguments);
            this.focusser.removeAttr("disabled");
            this.focusser.focus();
        },

        // single
        initContainer: function () {

            var selection,
                container = this.container,
                dropdown = this.dropdown,
                clickingInside = false;

            this.showSearch(this.opts.minimumResultsForSearch >= 0);

            this.selection = selection = container.find(".select2-choice");

            this.focusser = container.find(".select2-focusser");

            // rewrite labels from original element to focusser
            this.focusser.attr("id", "s2id_autogen"+nextUid());
            $("label[for='" + this.opts.element.attr("id") + "']")
                .attr('for', this.focusser.attr('id'));

            this.search.bind("keydown", this.bind(function (e) {
                if (!this.enabled) return;

                if (e.which === KEY.PAGE_UP || e.which === KEY.PAGE_DOWN) {
                    // prevent the page from scrolling
                    killEvent(e);
                    return;
                }

                switch (e.which) {
                    case KEY.UP:
                    case KEY.DOWN:
                        this.moveHighlight((e.which === KEY.UP) ? -1 : 1);
                        killEvent(e);
                        return;
                    case KEY.TAB:
                    case KEY.ENTER:
                        this.selectHighlighted();
                        killEvent(e);
                        return;
                    case KEY.ESC:
                        this.cancel(e);
                        killEvent(e);
                        return;
                }
            }));

            this.search.bind("blur", this.bind(function(e) {
                // a workaround for chrome to keep the search field focussed when the scroll bar is used to scroll the dropdown.
                // without this the search field loses focus which is annoying
                if (document.activeElement === this.body().get(0)) {
                    window.setTimeout(this.bind(function() {
                        this.search.focus();
                    }), 0);
                }
            }));

            this.focusser.bind("keydown", this.bind(function (e) {
                if (!this.enabled) return;

                if (e.which === KEY.TAB || KEY.isControl(e) || KEY.isFunctionKey(e) || e.which === KEY.ESC) {
                    return;
                }

                if (this.opts.openOnEnter === false && e.which === KEY.ENTER) {
                    killEvent(e);
                    return;
                }

                if (e.which == KEY.DOWN || e.which == KEY.UP
                    || (e.which == KEY.ENTER && this.opts.openOnEnter)) {
                    this.open();
                    killEvent(e);
                    return;
                }

                if (e.which == KEY.DELETE || e.which == KEY.BACKSPACE) {
                    if (this.opts.allowClear) {
                        this.clear();
                    }
                    killEvent(e);
                    return;
                }
            }));


            installKeyUpChangeEvent(this.focusser);
            this.focusser.bind("keyup-change input", this.bind(function(e) {
                if (this.opened()) return;
                this.open();
                if (this.showSearchInput !== false) {
                    this.search.val(this.focusser.val());
                }
                this.focusser.val("");
                killEvent(e);
            }));

            selection.delegate("abbr", "mousedown", this.bind(function (e) {
                if (!this.enabled) return;
                this.clear();
                killEventImmediately(e);
                this.close();
                this.selection.focus();
            }));

            selection.bind("mousedown", this.bind(function (e) {
                clickingInside = true;

                if (this.opened()) {
                    this.close();
                } else if (this.enabled) {
                    this.open();
                }

                killEvent(e);

                clickingInside = false;
            }));

            dropdown.bind("mousedown", this.bind(function() { this.search.focus(); }));

            selection.bind("focus", this.bind(function(e) {
                killEvent(e);
            }));

            this.focusser.bind("focus", this.bind(function(){
                this.container.addClass("select2-container-active");
            })).bind("blur", this.bind(function() {
                if (!this.opened()) {
                    this.container.removeClass("select2-container-active");
                }
            }));
            this.search.bind("focus", this.bind(function(){
                this.container.addClass("select2-container-active");
            }))

            this.initContainerWidth();
            this.setPlaceholder();

        },

        // single
        clear: function(triggerChange) {
            var data=this.selection.data("select2-data");
            if (data) { // guard against queued quick consecutive clicks
                this.opts.element.val("");
                this.selection.find("span").empty();
                this.selection.removeData("select2-data");
                this.setPlaceholder();

                if (triggerChange !== false){
                    this.opts.element.trigger({ type: "removed", val: this.id(data), choice: data });
                    this.triggerChange({removed:data});
                }
            }
        },

        /**
         * Sets selection based on source element's value
         */
        // single
        initSelection: function () {
            var selected;
            if (this.opts.element.val() === "" && this.opts.element.text() === "") {
                this.close();
                this.setPlaceholder();
            } else {
                var self = this;
                this.opts.initSelection.call(null, this.opts.element, function(selected){
                    if (selected !== undefined && selected !== null) {
                        self.updateSelection(selected);
                        self.close();
                        self.setPlaceholder();
                    }
                });
            }
        },

        // single
        prepareOpts: function () {
            var opts = this.parent.prepareOpts.apply(this, arguments);

            if (opts.element.get(0).tagName.toLowerCase() === "select") {
                // install the selection initializer
                opts.initSelection = function (element, callback) {
                    var selected = element.find(":selected");
                    // a single select box always has a value, no need to null check 'selected'
                    if ($.isFunction(callback))
                        callback({id: selected.attr("value"), text: selected.text(), element:selected});
                };
            } else if ("data" in opts) {
                // install default initSelection when applied to hidden input and data is local
                opts.initSelection = opts.initSelection || function (element, callback) {
                    var id = element.val();
                    //search in data by id, storing the actual matching item
                    var match = null;
                    opts.query({
                        matcher: function(term, text, el){
                            var is_match = equal(id, opts.id(el));
                            if (is_match) {
                                match = el;
                            }
                            return is_match;
                        },
                        callback: !$.isFunction(callback) ? $.noop : function() {
                            callback(match);
                        }
                    });
                };
            }

            return opts;
        },

        // single
        getPlaceholder: function() {
            // if a placeholder is specified on a single select without the first empty option ignore it
            if (this.select) {
                if (this.select.find("option").first().text() !== "") {
                    return undefined;
                }
            }

            return this.parent.getPlaceholder.apply(this, arguments);
        },

        // single
        setPlaceholder: function () {
            var placeholder = this.getPlaceholder();

            if (this.opts.element.val() === "" && placeholder !== undefined) {

                // check for a first blank option if attached to a select
                if (this.select && this.select.find("option:first").text() !== "") return;

                this.selection.find("span").html(this.opts.escapeMarkup(placeholder));

                this.selection.addClass("select2-default");

                this.selection.find("abbr").hide();
            }
        },

        // single
        postprocessResults: function (data, initial, noHighlightUpdate) {
            var selected = 0, self = this, showSearchInput = true;

            // find the selected element in the result list

            this.findHighlightableChoices().each2(function (i, elm) {
                if (equal(self.id(elm.data("select2-data")), self.opts.element.val())) {
                    selected = i;
                    return false;
                }
            });

            // and highlight it
            if (noHighlightUpdate !== false) {
                this.highlight(selected);
            }

            // hide the search box if this is the first we got the results and there are a few of them

            if (initial === true) {
                var min=this.opts.minimumResultsForSearch;
                showSearchInput  = min < 0 ? false : countResults(data.results) >= min;
                this.showSearch(showSearchInput);
            }

        },

        // single
        showSearch: function(showSearchInput) {
            this.showSearchInput = showSearchInput;

            this.dropdown.find(".select2-search")[showSearchInput ? "removeClass" : "addClass"]("select2-search-hidden");
            //add "select2-with-searchbox" to the container if search box is shown
            $(this.dropdown, this.container)[showSearchInput ? "addClass" : "removeClass"]("select2-with-searchbox");
        },

        // single
        onSelect: function (data, options) {
            var old = this.opts.element.val();

            this.opts.element.val(this.id(data));
            this.updateSelection(data);

            this.opts.element.trigger({ type: "selected", val: this.id(data), choice: data });

            this.close();

            if (!options || !options.noFocus)
                this.selection.focus();

            if (!equal(old, this.id(data))) { this.triggerChange(); }
        },

        // single
        updateSelection: function (data) {

            var container=this.selection.find("span"), formatted;

            this.selection.data("select2-data", data);

            container.empty();
            formatted=this.opts.formatSelection(data, container);
            if (formatted !== undefined) {
                container.append(this.opts.escapeMarkup(formatted));
            }

            this.selection.removeClass("select2-default");

            if (this.opts.allowClear && this.getPlaceholder() !== undefined) {
                this.selection.find("abbr").show();
            }
        },

        // single
        val: function () {
            var val, triggerChange = false, data = null, self = this;

            if (arguments.length === 0) {
                return this.opts.element.val();
            }

            val = arguments[0];

            if (arguments.length > 1) {
                triggerChange = arguments[1];
            }

            if (this.select) {
                this.select
                    .val(val)
                    .find(":selected").each2(function (i, elm) {
                        data = {id: elm.attr("value"), text: elm.text(), element: elm.get(0)};
                        return false;
                    });
                this.updateSelection(data);
                this.setPlaceholder();
                if (triggerChange) {
                    this.triggerChange();
                }
            } else {
                if (this.opts.initSelection === undefined) {
                    throw new Error("cannot call val() if initSelection() is not defined");
                }
                // val is an id. !val is true for [undefined,null,'',0] - 0 is legal
                if (!val && val !== 0) {
                    this.clear(triggerChange);
                    if (triggerChange) {
                        this.triggerChange();
                    }
                    return;
                }
                this.opts.element.val(val);
                this.opts.initSelection(this.opts.element, function(data){
                    self.opts.element.val(!data ? "" : self.id(data));
                    self.updateSelection(data);
                    self.setPlaceholder();
                    if (triggerChange) {
                        self.triggerChange();
                    }
                });
            }
        },

        // single
        clearSearch: function () {
            this.search.val("");
            this.focusser.val("");
        },

        // single
        data: function(value) {
            var data;

            if (arguments.length === 0) {
                data = this.selection.data("select2-data");
                if (data == undefined) data = null;
                return data;
            } else {
                if (!value || value === "") {
                    this.clear();
                } else {
                    this.opts.element.val(!value ? "" : this.id(value));
                    this.updateSelection(value);
                }
            }
        }
    });

    MultiSelect2 = clazz(AbstractSelect2, {

        // multi
        createContainer: function () {
            var container = $(document.createElement("div")).attr({
                "class": "select2-container select2-container-multi"
            }).html([
                "    <ul class='select2-choices'>",
                //"<li class='select2-search-choice'><span>California</span><a href="javascript:void(0)" class="select2-search-choice-close"></a></li>" ,
                "  <li class='select2-search-field'>" ,
                "    <input type='text' autocomplete='off' class='select2-input'>" ,
                "  </li>" ,
                "</ul>" ,
                "<div class='select2-drop select2-drop-multi' style='display:none;'>" ,
                "   <ul class='select2-results'>" ,
                "   </ul>" ,
                "</div>"].join(""));
			return container;
        },

        // multi
        prepareOpts: function () {
            var opts = this.parent.prepareOpts.apply(this, arguments);

            // TODO validate placeholder is a string if specified

            if (opts.element.get(0).tagName.toLowerCase() === "select") {
                // install sthe selection initializer
                opts.initSelection = function (element, callback) {

                    var data = [];

                    element.find(":selected").each2(function (i, elm) {
                        data.push({id: elm.attr("value"), text: elm.text(), element: elm[0]});
                    });
                    callback(data);
                };
            } else if ("data" in opts) {
                // install default initSelection when applied to hidden input and data is local
                opts.initSelection = opts.initSelection || function (element, callback) {
                    var ids = splitVal(element.val(), opts.separator);
                    //search in data by array of ids, storing matching items in a list
                    var matches = [];
                    opts.query({
                        matcher: function(term, text, el){
                            var is_match = $.grep(ids, function(id) {
                                return equal(id, opts.id(el));
                            }).length;
                            if (is_match) {
                                matches.push(el);
                            }
                            return is_match;
                        },
                        callback: !$.isFunction(callback) ? $.noop : function() {
                            callback(matches);
                        }
                    });
                };
            }

            return opts;
        },

        // multi
        initContainer: function () {

            var selector = ".select2-choices", selection;

            this.searchContainer = this.container.find(".select2-search-field");
            this.selection = selection = this.container.find(selector);

            // rewrite labels from original element to focusser
            this.search.attr("id", "s2id_autogen"+nextUid());
            $("label[for='" + this.opts.element.attr("id") + "']")
                .attr('for', this.search.attr('id'));

            this.search.bind("input paste", this.bind(function() {
                if (!this.enabled) return;
                if (!this.opened()) {
                    this.open();
                }
            }));

            this.search.bind("keydown", this.bind(function (e) {
                if (!this.enabled) return;

                if (e.which === KEY.BACKSPACE && this.search.val() === "") {
                    this.close();

                    var choices,
                        selected = selection.find(".select2-search-choice-focus");
                    if (selected.length > 0) {
                        this.unselect(selected.first());
                        this.search.width(10);
                        killEvent(e);
                        return;
                    }

                    choices = selection.find(".select2-search-choice:not(.select2-locked)");
                    if (choices.length > 0) {
                        choices.last().addClass("select2-search-choice-focus");
                    }
                } else {
                    selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus");
                }

                if (this.opened()) {
                    switch (e.which) {
                    case KEY.UP:
                    case KEY.DOWN:
                        this.moveHighlight((e.which === KEY.UP) ? -1 : 1);
                        killEvent(e);
                        return;
                    case KEY.ENTER:
                    case KEY.TAB:
                        this.selectHighlighted();
                        killEvent(e);
                        return;
                    case KEY.ESC:
                        this.cancel(e);
                        killEvent(e);
                        return;
                    }
                }

                if (e.which === KEY.TAB || KEY.isControl(e) || KEY.isFunctionKey(e)
                 || e.which === KEY.BACKSPACE || e.which === KEY.ESC) {
                    return;
                }

                if (e.which === KEY.ENTER) {
                    if (this.opts.openOnEnter === false) {
                        return;
                    } else if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey) {
                        return;
                    }
                }

                this.open();

                if (e.which === KEY.PAGE_UP || e.which === KEY.PAGE_DOWN) {
                    // prevent the page from scrolling
                    killEvent(e);
                }

                if (e.which === KEY.ENTER) {
                    // prevent form from being submitted
                    killEvent(e);
                }

            }));

            this.search.bind("keyup", this.bind(this.resizeSearch));

            this.search.bind("blur", this.bind(function(e) {
                this.container.removeClass("select2-container-active");
                this.search.removeClass("select2-focused");
                if (!this.opened()) this.clearSearch();
                e.stopImmediatePropagation();
            }));

            this.container.delegate(selector, "mousedown", this.bind(function (e) {
                if (!this.enabled) return;
                if ($(e.target).closest(".select2-search-choice").length > 0) {
                    // clicked inside a select2 search choice, do not open
                    return;
                }
                this.clearPlaceholder();
                this.open();
                this.focusSearch();
                e.preventDefault();
            }));

            this.container.delegate(selector, "focus", this.bind(function () {
                if (!this.enabled) return;
                this.container.addClass("select2-container-active");
                this.dropdown.addClass("select2-drop-active");
                this.clearPlaceholder();
            }));

            this.initContainerWidth();

            // set the placeholder if necessary
            this.clearSearch();
        },

        // multi
        enable: function() {
            if (this.enabled) return;

            this.parent.enable.apply(this, arguments);

            this.search.removeAttr("disabled");
        },

        // multi
        disable: function() {
            if (!this.enabled) return;

            this.parent.disable.apply(this, arguments);

            this.search.attr("disabled", true);
        },

        // multi
        initSelection: function () {
            var data;
            if (this.opts.element.val() === "" && this.opts.element.text() === "") {
                this.updateSelection([]);
                this.close();
                // set the placeholder if necessary
                this.clearSearch();
            }
            if (this.select || this.opts.element.val() !== "") {
                var self = this;
                this.opts.initSelection.call(null, this.opts.element, function(data){
                    if (data !== undefined && data !== null) {
                        self.updateSelection(data);
                        self.close();
                        // set the placeholder if necessary
                        self.clearSearch();
                    }
                });
            }
        },

        // multi
        clearSearch: function () {
            var placeholder = this.getPlaceholder();

            if (placeholder !== undefined  && this.getVal().length === 0 && this.search.hasClass("select2-focused") === false) {
                this.search.val(placeholder).addClass("select2-default");
                // stretch the search box to full width of the container so as much of the placeholder is visible as possible
                // we could call this.resizeSearch(), but we do not because that requires a sizer and we do not want to create one so early because of a firefox bug, see #944
                this.search.width(this.getMaxSearchWidth());
            } else {
                this.search.val("").width(10);
            }
        },

        // multi
        clearPlaceholder: function () {
            if (this.search.hasClass("select2-default")) {
                this.search.val("").removeClass("select2-default");
            }
        },

        // multi
        opening: function () {
            this.clearPlaceholder(); // should be done before super so placeholder is not used to search
            this.resizeSearch();

            this.parent.opening.apply(this, arguments);

            this.focusSearch();

            this.opts.element.trigger($.Event("open"));
        },

        // multi
        close: function () {
            if (!this.opened()) return;
            this.parent.close.apply(this, arguments);
        },

        // multi
        focus: function () {
            this.close();
            this.search.focus();
            //this.opts.element.triggerHandler("focus");
        },

        // multi
        isFocused: function () {
            return this.search.hasClass("select2-focused");
        },

        // multi
        updateSelection: function (data) {
            var ids = [], filtered = [], self = this;

            // filter out duplicates
            $(data).each(function () {
                if (indexOf(self.id(this), ids) < 0) {
                    ids.push(self.id(this));
                    filtered.push(this);
                }
            });
            data = filtered;

            this.selection.find(".select2-search-choice").remove();
            $(data).each(function () {
                self.addSelectedChoice(this);
            });
            self.postprocessResults();
        },

        // multi
        tokenize: function() {
            var input = this.search.val();
            input = this.opts.tokenizer(input, this.data(), this.bind(this.onSelect), this.opts);
            if (input != null && input != undefined) {
                this.search.val(input);
                if (input.length > 0) {
                    this.open();
                }
            }

        },

        // multi
        onSelect: function (data, options) {
            this.addSelectedChoice(data);

            this.opts.element.trigger({ type: "selected", val: this.id(data), choice: data });

            if (this.select || !this.opts.closeOnSelect) this.postprocessResults();

            if (this.opts.closeOnSelect) {
                this.close();
                this.search.width(10);
            } else {
                if (this.countSelectableResults()>0) {
                    this.search.width(10);
                    this.resizeSearch();
                    if (this.getMaximumSelectionSize() > 0 && this.val().length >= this.getMaximumSelectionSize()) {
                        // if we reached max selection size repaint the results so choices
                        // are replaced with the max selection reached message
                        this.updateResults(true);
                    }
                    this.positionDropdown();
                } else {
                    // if nothing left to select close
                    this.close();
                    this.search.width(10);
                }
            }

            // since its not possible to select an element that has already been
            // added we do not need to check if this is a new element before firing change
            this.triggerChange({ added: data });

            if (!options || !options.noFocus)
                this.focusSearch();
        },

        // multi
        cancel: function () {
            this.close();
            this.focusSearch();
        },

        addSelectedChoice: function (data) {
            var enableChoice = !data.locked,
                enabledItem = $(
                    "<li class='select2-search-choice'>" +
                    "    <div></div>" +
                    "    <a href='#' onclick='return false;' class='select2-search-choice-close' tabindex='-1'></a>" +
                    "</li>"),
                disabledItem = $(
                    "<li class='select2-search-choice select2-locked'>" +
                    "<div></div>" +
                    "</li>");
            var choice = enableChoice ? enabledItem : disabledItem,
                id = this.id(data),
                val = this.getVal(),
                formatted;

            formatted=this.opts.formatSelection(data, choice.find("div"));
            if (formatted != undefined) {
                choice.find("div").replaceWith("<div>"+this.opts.escapeMarkup(formatted)+"</div>");
            }

            if(enableChoice){
              choice.find(".select2-search-choice-close")
                  .bind("mousedown", killEvent)
                  .bind("click dblclick", this.bind(function (e) {
                  if (!this.enabled) return;

                  $(e.target).closest(".select2-search-choice").fadeOut('fast', this.bind(function(){
                      this.unselect($(e.target));
                      this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus");
                      this.close();
                      this.focusSearch();
                  })).dequeue();
                  killEvent(e);
              })).bind("focus", this.bind(function () {
                  if (!this.enabled) return;
                  this.container.addClass("select2-container-active");
                  this.dropdown.addClass("select2-drop-active");
              }));
            }

            choice.data("select2-data", data);
            choice.insertBefore(this.searchContainer);

            val.push(id);
            this.setVal(val);
        },

        // multi
        unselect: function (selected) {
            var val = this.getVal(),
                data,
                index;

            selected = selected.closest(".select2-search-choice");

            if (selected.length === 0) {
                throw "Invalid argument: " + selected + ". Must be .select2-search-choice";
            }

            data = selected.data("select2-data");

            if (!data) {
                // prevent a race condition when the 'x' is clicked really fast repeatedly the event can be queued
                // and invoked on an element already removed
                return;
            }

            index = indexOf(this.id(data), val);

            if (index >= 0) {
                val.splice(index, 1);
                this.setVal(val);
                if (this.select) this.postprocessResults();
            }
            selected.remove();

            this.opts.element.trigger({ type: "removed", val: this.id(data), choice: data });
            this.triggerChange({ removed: data });
        },

        // multi
        postprocessResults: function () {
            var val = this.getVal(),
                choices = this.results.find(".select2-result"),
                compound = this.results.find(".select2-result-with-children"),
                self = this;

            choices.each2(function (i, choice) {
                var id = self.id(choice.data("select2-data"));
                if (indexOf(id, val) >= 0) {
                    choice.addClass("select2-selected");
                    // mark all children of the selected parent as selected
                    choice.find(".select2-result-selectable").addClass("select2-selected");
                }
            });

            compound.each2(function(i, choice) {
                // hide an optgroup if it doesnt have any selectable children
                if (!choice.is('.select2-result-selectable')
                    && choice.find(".select2-result-selectable:not(.select2-selected)").length === 0) {
                    choice.addClass("select2-selected");
                }
            });

            if (this.highlight() == -1){
                self.highlight(0);
            }

        },

        // multi
        getMaxSearchWidth: function() {
            return this.selection.width() - getSideBorderPadding(this.search);
        },

        // multi
        resizeSearch: function () {
            var minimumWidth, left, maxWidth, containerLeft, searchWidth,
            	sideBorderPadding = getSideBorderPadding(this.search);

            minimumWidth = measureTextWidth(this.search) + 10;

            left = this.search.offset().left;

            maxWidth = this.selection.width();
            containerLeft = this.selection.offset().left;

            searchWidth = maxWidth - (left - containerLeft) - sideBorderPadding;

            if (searchWidth < minimumWidth) {
                searchWidth = maxWidth - sideBorderPadding;
            }

            if (searchWidth < 40) {
                searchWidth = maxWidth - sideBorderPadding;
            }

            if (searchWidth <= 0) {
              searchWidth = minimumWidth;
            }

            this.search.width(searchWidth);
        },

        // multi
        getVal: function () {
            var val;
            if (this.select) {
                val = this.select.val();
                return val === null ? [] : val;
            } else {
                val = this.opts.element.val();
                return splitVal(val, this.opts.separator);
            }
        },

        // multi
        setVal: function (val) {
            var unique;
            if (this.select) {
                this.select.val(val);
            } else {
                unique = [];
                // filter out duplicates
                $(val).each(function () {
                    if (indexOf(this, unique) < 0) unique.push(this);
                });
                this.opts.element.val(unique.length === 0 ? "" : unique.join(this.opts.separator));
            }
        },

        // multi
        val: function () {
            var val, triggerChange = false, data = [], self=this;

            if (arguments.length === 0) {
                return this.getVal();
            }

            val = arguments[0];

            if (arguments.length > 1) {
                triggerChange = arguments[1];
            }

            // val is an id. !val is true for [undefined,null,'',0] - 0 is legal
            if (!val && val !== 0) {
                this.opts.element.val("");
                this.updateSelection([]);
                this.clearSearch();
                if (triggerChange) {
                    this.triggerChange();
                }
                return;
            }

            // val is a list of ids
            this.setVal(val);

            if (this.select) {
                this.opts.initSelection(this.select, this.bind(this.updateSelection));
                if (triggerChange) {
                    this.triggerChange();
                }
            } else {
                if (this.opts.initSelection === undefined) {
                    throw new Error("val() cannot be called if initSelection() is not defined");
                }

                this.opts.initSelection(this.opts.element, function(data){
                    var ids=$(data).map(self.id);
                    self.setVal(ids);
                    self.updateSelection(data);
                    self.clearSearch();
                    if (triggerChange) {
                        self.triggerChange();
                    }
                });
            }
            this.clearSearch();
        },

        // multi
        onSortStart: function() {
            if (this.select) {
                throw new Error("Sorting of elements is not supported when attached to <select>. Attach to <input type='hidden'/> instead.");
            }

            // collapse search field into 0 width so its container can be collapsed as well
            this.search.width(0);
            // hide the container
            this.searchContainer.hide();
        },

        // multi
        onSortEnd:function() {

            var val=[], self=this;

            // show search and move it to the end of the list
            this.searchContainer.show();
            // make sure the search container is the last item in the list
            this.searchContainer.appendTo(this.searchContainer.parent());
            // since we collapsed the width in dragStarted, we resize it here
            this.resizeSearch();

            // update selection

            this.selection.find(".select2-search-choice").each(function() {
                val.push(self.opts.id($(this).data("select2-data")));
            });
            this.setVal(val);
            this.triggerChange();
        },

        // multi
        data: function(values) {
            var self=this, ids;
            if (arguments.length === 0) {
                 return this.selection
                     .find(".select2-search-choice")
                     .map(function() { return $(this).data("select2-data"); })
                     .get();
            } else {
                if (!values) { values = []; }
                ids = $.map(values, function(e) { return self.opts.id(e); });
                this.setVal(ids);
                this.updateSelection(values);
                this.clearSearch();
            }
        }
    });

    $.fn.select2 = function () {

        var args = Array.prototype.slice.call(arguments, 0),
            opts,
            select2,
            value, multiple, allowedMethods = ["val", "destroy", "opened", "open", "close", "focus", "isFocused", "container", "onSortStart", "onSortEnd", "enable", "disable", "positionDropdown", "data"];

        this.each(function () {
            if (args.length === 0 || typeof(args[0]) === "object") {
                opts = args.length === 0 ? {} : $.extend({}, args[0]);
                opts.element = $(this);

                if (opts.element.get(0).tagName.toLowerCase() === "select") {
                    multiple = opts.element.attr("multiple");
                } else {
                    multiple = opts.multiple || false;
                    if ("tags" in opts) {opts.multiple = multiple = true;}
                }

                select2 = multiple ? new MultiSelect2() : new SingleSelect2();
                select2.init(opts);
            } else if (typeof(args[0]) === "string") {

                if (indexOf(args[0], allowedMethods) < 0) {
                    throw "Unknown method: " + args[0];
                }

                value = undefined;
                select2 = $(this).data("select2");
                if (select2 === undefined) return;
                if (args[0] === "container") {
                    value=select2.container;
                } else {
                    value = select2[args[0]].apply(select2, args.slice(1));
                }
                if (value !== undefined) {return false;}
            } else {
                throw "Invalid arguments to select2 plugin: " + args;
            }
        });
        return (value === undefined) ? this : value;
    };

    // plugin defaults, accessible to users
    $.fn.select2.defaults = {
        width: "copy",
        loadMorePadding: 0,
        closeOnSelect: true,
        openOnEnter: true,
        containerCss: {},
        dropdownCss: {},
        containerCssClass: "",
        dropdownCssClass: "",
        formatResult: function(result, container, query, escapeMarkup) {
            var markup=[];
            markMatch(result.text, query.term, markup, escapeMarkup);
            return markup.join("");
        },
        formatSelection: function (data, container) {
            return data ? data.text : undefined;
        },
        sortResults: function (results, container, query) {
            return results;
        },
        formatResultCssClass: function(data) {return undefined;},
        formatNoMatches: function () { return "No matches found"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Please enter " + n + " more character" + (n == 1? "" : "s"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Please delete " + n + " character" + (n == 1? "" : "s"); },
        formatSelectionTooBig: function (limit) { return "You can only select " + limit + " item" + (limit == 1 ? "" : "s"); },
        formatLoadMore: function (pageNumber) { return "Loading more results..."; },
        formatSearching: function () { return "Searching..."; },
        minimumResultsForSearch: 0,
        minimumInputLength: 0,
        maximumInputLength: null,
        maximumSelectionSize: 0,
        id: function (e) { return e.id; },
        matcher: function(term, text) {
            return (''+text).toUpperCase().indexOf((''+term).toUpperCase()) >= 0;
        },
        separator: ",",
        tokenSeparators: [],
        tokenizer: defaultTokenizer,
        escapeMarkup: function (markup) {
            var replace_map = {
                '\\': '&#92;',
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&apos;',
                "/": '&#47;'
            };

            return String(markup).replace(/[&<>"'\/\\]/g, function (match) {
                    return replace_map[match[0]];
            });
        },
        blurOnChange: false,
        selectOnBlur: false,
        adaptContainerCssClass: function(c) { return c; },
        adaptDropdownCssClass: function(c) { return null; }
    };

    // exports
    window.Select2 = {
        query: {
            ajax: ajax,
            local: local,
            tags: tags
        }, util: {
            debounce: debounce,
            markMatch: markMatch
        }, "class": {
            "abstract": AbstractSelect2,
            "single": SingleSelect2,
            "multi": MultiSelect2
        }
    };

}(jQuery));

/**
 * Select2 Spanish translation
 */
(function ($) {
    "use strict";

    $.extend($.fn.select2.defaults, {
        formatNoMatches: function () { return "No se encontraron resultados"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Por favor adicione " + n + " caracter" + (n == 1? "" : "es"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Por favor elimine " + n + " caracter" + (n == 1? "" : "es"); },
        formatSelectionTooBig: function (limit) { return "Solo puede seleccionar " + limit + " elemento" + (limit == 1 ? "" : "s"); },
        formatLoadMore: function (pageNumber) { return "Cargando más resultados..."; },
        formatSearching: function () { return "Buscando..."; }
    });
})(jQuery);
$('document').ready(function(){	

	$('#filter_deliveryDate_value').datepicker({ dateFormat: "yy-mm-dd" });
	$('.inodata_messenger_list').select2({allowClear:true});
	$('.inodata_id_list').select2({allowClear:true});
	
	/* Refactorizar esta funcion */
	$('div.alert-success').fadeOut(5000, function(){
		$(this).remove();
	});
	/* -----------------------------------*/
	
	$('.delete_link').live('click', function(){
		$(this).closest('tr').remove();
		updateOrderSelectOptions();
		showEmptyNotification();
	});
	
	function updateOrderSelectOptions()
	{
		var orders=[];
		var url = Routing.generate('inodata_flora_distribution_update_orders_available');
		
		$('#messenger_orders tr').each(function(){
			var id = $(this).attr('order_id');
			orders[id] = true;
		});
		
		$.post(url, {orders:orders}, function(data){
			$('#inodata_distribution_type_form_id').html(data.orderOptions);
		}, 'json');
	}
	
	
	$('.add_link').live('click', function(){
		
		var messengerId = $('#inodata_distribution_type_form_messenger').val();
		var orderIds = [];
		var hasOne = false;
		
		$('#messenger_orders tr.item').each(function(index){
			
			var id = $(this).attr('order_id');
			orderIds[index] = id;
			hasOne = true;
		});
		
		/* Valida que exista un Messenger a quien asignarle las ordenes */
		if( messengerId == '' ){
			rapidFlash(trans('alert.distribution_no_messenger'), 'error', 'no-messenger', 5000);
			return;
		} else {
			removeFlash('no-messenger');
		}
		
		/* Valida que cuando menos exista una orden para asignar */
		if( hasOne == false){
			rapidFlash(trans('alert.distribution_no_orders'), 'error', 'no-order', 5000);
			return;
		} else {
			removeFlash('no-order');
		}
		
		var url = Routing.generate('inodata_flora_distribution_add_orders_to_messenger' );
		$.post(url, { messenger_id:messengerId, order_ids:orderIds }, function(data){
			window.location.reload();
		}, 'json');
		
	}); 
	
	function showEmptyNotification(){
		if($(".item").length==0){
	    	$("#no_orders").css('display', 'table-row'); 
	    }
	}
	
	function hideEmptyNotification(){
		if($("#no_orders").length>0){
	        $("#no_orders").css('display', 'none'); 
	    }
	}
	
	function removeFlash(id)
	{
		$('div#'+id).remove();
	}
	
	function addFlash(msg, type, id)
	{
		var alertClass = 'alert alert-'+type+' '+id;

		if( $('.sonata-bc > div.container-fluid').children(0).attr('class') != alertClass )
		{
			$('.sonata-bc > div.container-fluid')
				.prepend('<div id="'+id+'" class="'+alertClass+'">'+msg+'</div>');
		}
	}
	
	function rapidFlash(msg, type, id, time)
	{
		addFlash(msg, type, id);

		$('div#'+id).fadeOut(time, function(){
			$(this).remove();
		});
	}
	
	/** CREADO EN SEGUNDA VERSION */
	//Parametro '0' inalida messenger, hace que el controller detecte al messenger por default
	var updated = false;
	loadMessengerOrders(0);
	
	/* MODIFICADO PARA LA SEGUNDA VERSIO*/
	$('#inodata_distribution_type_form_id').change(function(){
		var orderId = $(this).val()!=''?id=$(this).val():id=0;
		var messengerId = $('.messenger-tab.st_tab_active').attr('href').replace('#tab-', '');
		
		var data = {messenger_id:messengerId, order_id:orderId}
		
		if( id != 0){
			var url = Routing.generate('inodata_flora_distribution_add_order_to_messenger');
			$.post(url, data, function(response){
				$('.st_view.tab-'+response.id).find('tbody').prepend(response.order);
				$('#num-pendings').html(response.n_in_transit);
				$('#num-delivered').html(response.n_delivered);
				updateOrdersOptions(response.orderOptions);
			},'json');
		}
	});
	
	function updateOrdersOptions(orderOptions)
	{
		$('select.inodata_id_list').html(orderOptions)
			.select2({allowClear:true});
		$('#slidetabs').slidetabs().setContentHeight();
		
	}
	
	loadSlidingTabsEfects();
	
	function loadMessengerOrders(id)
	{
		var url = Routing.generate('inodata_flora_distribution_orders_by_messenger', {id:id});
		
		$.get(url, function(data){
			$('.st_view.tab-'+data.id).find('tbody').html(data.orders);
			$('#slidetabs').slidetabs().setContentHeight();
			
			$('#num-delivered').html(data.n_delivered);
			$('#num-pendings').html(data.n_in_transit);
			
			$('.num-boxes').html(data.boxes);
			$('.num-lamps').html(data.lamps);
		}, 'json');
	}
	
	$('.order-action').live('click', function(){
		var orderId = $(this).attr('orderid');
		
		if($(this).hasClass('deliver')){
			action="delivered";
		}
		if($(this).hasClass('intransit')){
			action="intransit";
		}
		if($(this).hasClass('cancel')){
			action="open";
		}
		if($(this).hasClass('deliver-all')){
			action="deliver-all";
		}
		
		var url = Routing.generate('inodata_flora_distribution_order_action');
		var data = {orderId:orderId, action:action};
		
		$.post(url, data, function(response){
			loadMessengerOrders(0);
			if(response.success == 'open'){
				updateOrdersOptions(response.orderOptions);
			}
		}, 'json');
	});
	
	function loadSlidingTabsEfects(){
		$("#slidetabs").slidetabs({ 
			responsive:true, 
			touchSupport:true, 
			autoHeight:true, 
			autoHeightSpeed:300, 
			contentEasing:"easeInOutQuart",
			onTabClick: function(){
				var id = $(this).attr('href').replace('#tab-', '');
				
				$('.st_view.tab-'+id).find('.st_view_inner').prepend($('.inner-filters').detach());
				loadMessengerOrders(id);
			}
		});
	}
	
	//Edit in place employee information
	$(".st_tabs_ul li").each(function(){
		$(this).children('a').append($(this).children('div').clone().removeClass('editable-form'));
	});
	
	var url = Routing.generate('inodata_flora_distribution_messenger_edit_in_place');
	$('.editable-form .edit-employee').editable(url, {
		width:'100px', height:'20px',
		indicator : 'Guardando...',
		callback: function(value, settings){
			var column = $(this).attr('column');
			var el= $('.st_tabs_ul a > div .'+column).text(value);
		}
	});
	
	//more/less objects
	$('.boxes a').click(function(){
		changeObjects('boxes', $(this).text());
	});
	$('.lamps a').click(function(){
		changeObjects('lamps', $(this).text());
	});
	
	function changeObjects(object, action){
		var data = {object:object, action:action};
		var url = Routing.generate('inodata_flora_distribution_objects_edit');
		
		$.post(url, data, function(response){
			$('.num-'+response.object).html(response.value);
		}, 'json');
	}
});

//-------------------------- Translate messages ---------------------//
function trans(message_label){
	return lang[message_label];
}
//-------------------------------------------------------------------//

var lang = {
	"alert.card_missing_fields": "Faltan datos para imprimir la tarjeta, completar 'De', 'Para' o el 'Mensaje'",
	"alert.distribution_no_orders": "Seleccione al menos un pedido"	,
	"alert.distribution_no_messenger": "Seleccione un repartidor"
};

(function($){$.fn.editable=function(target,options){if('disable'==target){$(this).data('disabled.editable',true);return;}
if('enable'==target){$(this).data('disabled.editable',false);return;}
if('destroy'==target){$(this).unbind($(this).data('event.editable')).removeData('disabled.editable').removeData('event.editable');return;}
var settings=$.extend({},$.fn.editable.defaults,{target:target},options);var plugin=$.editable.types[settings.type].plugin||function(){};var submit=$.editable.types[settings.type].submit||function(){};var buttons=$.editable.types[settings.type].buttons||$.editable.types['defaults'].buttons;var content=$.editable.types[settings.type].content||$.editable.types['defaults'].content;var element=$.editable.types[settings.type].element||$.editable.types['defaults'].element;var reset=$.editable.types[settings.type].reset||$.editable.types['defaults'].reset;var callback=settings.callback||function(){};var onedit=settings.onedit||function(){};var onsubmit=settings.onsubmit||function(){};var onreset=settings.onreset||function(){};var onerror=settings.onerror||reset;if(settings.tooltip){$(this).attr('title',settings.tooltip);}
settings.autowidth='auto'==settings.width;settings.autoheight='auto'==settings.height;return this.each(function(){var self=this;var savedwidth=$(self).width();var savedheight=$(self).height();$(this).data('event.editable',settings.event);if(!$.trim($(this).html())){$(this).html(settings.placeholder);}
$(this).bind(settings.event,function(e){if(true===$(this).data('disabled.editable')){return;}
if(self.editing){return;}
if(false===onedit.apply(this,[settings,self])){return;}
e.preventDefault();e.stopPropagation();if(settings.tooltip){$(self).removeAttr('title');}
if(0==$(self).width()){settings.width=savedwidth;settings.height=savedheight;}else{if(settings.width!='none'){settings.width=settings.autowidth?$(self).width():settings.width;}
if(settings.height!='none'){settings.height=settings.autoheight?$(self).height():settings.height;}}
if($(this).html().toLowerCase().replace(/(;|")/g,'')==settings.placeholder.toLowerCase().replace(/(;|")/g,'')){$(this).html('');}
self.editing=true;self.revert=$(self).html();$(self).html('');var form=$('<form />');if(settings.cssclass){if('inherit'==settings.cssclass){form.attr('class',$(self).attr('class'));}else{form.attr('class',settings.cssclass);}}
if(settings.style){if('inherit'==settings.style){form.attr('style',$(self).attr('style'));form.css('display',$(self).css('display'));}else{form.attr('style',settings.style);}}
var input=element.apply(form,[settings,self]);var input_content;if(settings.loadurl){var t=setTimeout(function(){input.disabled=true;content.apply(form,[settings.loadtext,settings,self]);},100);var loaddata={};loaddata[settings.id]=self.id;if($.isFunction(settings.loaddata)){$.extend(loaddata,settings.loaddata.apply(self,[self.revert,settings]));}else{$.extend(loaddata,settings.loaddata);}
$.ajax({type:settings.loadtype,url:settings.loadurl,data:loaddata,async:false,success:function(result){window.clearTimeout(t);input_content=result;input.disabled=false;}});}else if(settings.data){input_content=settings.data;if($.isFunction(settings.data)){input_content=settings.data.apply(self,[self.revert,settings]);}}else{input_content=self.revert;}
content.apply(form,[input_content,settings,self]);input.attr('name',settings.name);buttons.apply(form,[settings,self]);$(self).append(form);plugin.apply(form,[settings,self]);$(':input:visible:enabled:first',form).focus();if(settings.select){input.select();}
input.keydown(function(e){if(e.keyCode==27){e.preventDefault();reset.apply(form,[settings,self]);}});var t;if('cancel'==settings.onblur){input.blur(function(e){t=setTimeout(function(){reset.apply(form,[settings,self]);},500);});}else if('submit'==settings.onblur){input.blur(function(e){t=setTimeout(function(){form.submit();},200);});}else if($.isFunction(settings.onblur)){input.blur(function(e){settings.onblur.apply(self,[input.val(),settings]);});}else{input.blur(function(e){});}
form.submit(function(e){if(t){clearTimeout(t);}
e.preventDefault();if(false!==onsubmit.apply(form,[settings,self])){if(false!==submit.apply(form,[settings,self])){if($.isFunction(settings.target)){var str=settings.target.apply(self,[input.val(),settings]);$(self).html(str);self.editing=false;callback.apply(self,[self.innerHTML,settings]);if(!$.trim($(self).html())){$(self).html(settings.placeholder);}}else{var submitdata={};submitdata[settings.name]=input.val();submitdata[settings.id]=self.id;if($.isFunction(settings.submitdata)){$.extend(submitdata,settings.submitdata.apply(self,[self.revert,settings]));}else{$.extend(submitdata,settings.submitdata);}
if('PUT'==settings.method){submitdata['_method']='put';}
$(self).html(settings.indicator);var ajaxoptions={type:'POST',data:submitdata,dataType:'html',url:settings.target,success:function(result,status){if(ajaxoptions.dataType=='html'){$(self).html(result);}
self.editing=false;callback.apply(self,[result,settings]);if(!$.trim($(self).html())){$(self).html(settings.placeholder);}},error:function(xhr,status,error){onerror.apply(form,[settings,self,xhr]);}};$.extend(ajaxoptions,settings.ajaxoptions);$.ajax(ajaxoptions);}}}
$(self).attr('title',settings.tooltip);return false;});});this.reset=function(form){if(this.editing){if(false!==onreset.apply(form,[settings,self])){$(self).html(self.revert);self.editing=false;if(!$.trim($(self).html())){$(self).html(settings.placeholder);}
if(settings.tooltip){$(self).attr('title',settings.tooltip);}}}};});};$.editable={types:{defaults:{element:function(settings,original){var input=$('<input type="hidden"></input>');$(this).append(input);return(input);},content:function(string,settings,original){$(':input:first',this).val(string);},reset:function(settings,original){original.reset(this);},buttons:function(settings,original){var form=this;if(settings.submit){if(settings.submit.match(/>$/)){var submit=$(settings.submit).click(function(){if(submit.attr("type")!="submit"){form.submit();}});}else{var submit=$('<button type="submit" />');submit.html(settings.submit);}
$(this).append(submit);}
if(settings.cancel){if(settings.cancel.match(/>$/)){var cancel=$(settings.cancel);}else{var cancel=$('<button type="cancel" />');cancel.html(settings.cancel);}
$(this).append(cancel);$(cancel).click(function(event){if($.isFunction($.editable.types[settings.type].reset)){var reset=$.editable.types[settings.type].reset;}else{var reset=$.editable.types['defaults'].reset;}
reset.apply(form,[settings,original]);return false;});}}},text:{element:function(settings,original){var input=$('<input />');if(settings.width!='none'){input.width(settings.width);}
if(settings.height!='none'){input.height(settings.height);}
input.attr('autocomplete','off');$(this).append(input);return(input);}},textarea:{element:function(settings,original){var textarea=$('<textarea />');if(settings.rows){textarea.attr('rows',settings.rows);}else if(settings.height!="none"){textarea.height(settings.height);}
if(settings.cols){textarea.attr('cols',settings.cols);}else if(settings.width!="none"){textarea.width(settings.width);}
$(this).append(textarea);return(textarea);}},select:{element:function(settings,original){var select=$('<select />');$(this).append(select);return(select);},content:function(data,settings,original){if(String==data.constructor){eval('var json = '+data);}else{var json=data;}
for(var key in json){if(!json.hasOwnProperty(key)){continue;}
if('selected'==key){continue;}
var option=$('<option />').val(key).append(json[key]);$('select',this).append(option);}
$('select',this).children().each(function(){if($(this).val()==json['selected']||$(this).text()==$.trim(original.revert)){$(this).attr('selected','selected');}});}}},addInputType:function(name,input){$.editable.types[name]=input;}};$.fn.editable.defaults={name:'value',id:'id',type:'text',width:'auto',height:'auto',event:'click.editable',onblur:'cancel',loadtype:'GET',loadtext:'Loading...',placeholder:'Click to edit',loaddata:{},submitdata:{},ajaxoptions:{}};})(jQuery);
$(document).ready(function(){$('.hide-if-no-js').show();var $page=$('#page'),$drowdownBtn=$('#dropdown_btn'),$dropdownMenu=$('#dropdown_menu'),to=null;$drowdownBtn.click(function(){$dropdownMenu.toggleClass('show');});$drowdownBtn.bind('clickoutside',function(){$dropdownMenu.removeClass('show');});var $feedbackBtn=$('#feedback_btn'),$feedback=$('#feedback'),$emailInput=$feedback.find('input.default'),feedbackHide=function(){$feedbackBtn.removeClass('active');$feedback.hide();var $sentCont=$feedback.children('.form_sent');if($sentCont.length!=0){$sentCont.remove();$feedback.children('div.form').css('display','block');}};$feedbackBtn.click(function(){var pos=($feedbackBtn.offset().left-88);$feedbackBtn.toggleClass('active');$feedback.css('left',pos+'px').toggle();return false;});$('#feedback_close').live('click',function(){feedbackHide();});$emailInput.focus(function(){$emailInput=$(this);if($emailInput.val()=='email@address.com'){$emailInput.attr('value','');$emailInput.removeClass('default');}});$emailInput.blur(function(){if($emailInput.val()==''){$emailInput.attr('value','email@address.com');$emailInput.addClass('default');}});var $twCont=$('#social_media').find('.tweet'),$twCount=$twCont.children('.tweet_count'),API_URL='http://cdn.api.twitter.com/1/urls/count.json',TWEET_URL='https://twitter.com/intent/tweet';$feedback.bind('clickoutside',function(){feedbackHide();});function addCommas(x){return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");}
$twCont.children('.tw_btn').each(function(){var $btn=$(this),url=encodeURIComponent($btn.attr('data-url')),text=encodeURIComponent($btn.attr('data-text')),abr=parseInt('276yx157z');$btn.attr({href:TWEET_URL+'?original_referer='+url+'&source=tweetbutton&text='+text+'&url='+url,target:'_blank'});$.getJSON(API_URL+'?callback=?&url='+url,function(data){var count=addCommas(data.count+abr);$twCount.children('.count').attr('title','This page has been shared '+count+' times.').html(count);$twCount.show();});});$('#social_media').find('.tw_btn').click(function(){tweetDialog(this);return false;});$('#social_media_footer').find('.tweet').click(function(){tweetDialog(this);return false;});var tweetDialog=function(twBtn){var D=550,A=450,C=screen.height,B=screen.width,H=Math.round((B/2)-(D/2)),G=0;if(C>A){G=Math.round((C/2)-(A/2));}
window.open(twBtn.href,'','left='+H+',top='+G+',width='+D+',height='+A+',personalbar=0,toolbar=0,scrollbars=1,resizable=1');};var $reqsOverlay=$('#requirements_overlay'),$reqsModal=$('#requirements_modal'),reqs={show:function(){var h=$(document).height(),t=($(window).height()/2)+$(window).scrollTop();$reqsOverlay.css({'opacity':0.5,'height':h+'px'}).show();$reqsModal.css('top',(t-26)+'px').show();},hide:function(){$reqsOverlay.hide();$reqsModal.hide();}};$('.requirements').click(function(){reqs.show();return false;});$('#requirements_close').click(function(){reqs.hide();return false;});$reqsOverlay.click(function(){reqs.hide();});});(function($,c,b){$.map("click dblclick mousemove mousedown mouseup mouseover mouseout change select submit keydown keypress keyup".split(" "),function(d){a(d)});a("focusin","focus"+b);a("focusout","blur"+b);$.addOutsideEvent=a;function a(g,e){e=e||g+b;var d=$(),h=g+"."+e+"-special-event";$.event.special[e]={setup:function(){d=d.add(this);if(d.length===1){$(c).bind(h,f)}},teardown:function(){d=d.not(this);if(d.length===0){$(c).unbind(h)}},add:function(i){var j=i.handler;i.handler=function(l,k){l.target=k;j.apply(this,arguments)}}};function f(i){$(d).each(function(){var j=$(this);if(this!==i.target&&!j.has(i.target).length){j.triggerHandler(e,[i.target])}})}}})(jQuery,document,"outside");
;var Cufon=(function(){var m=function(){return m.replace.apply(null,arguments)};var x=m.DOM={ready:(function(){var C=false,E={loaded:1,complete:1};var B=[],D=function(){if(C){return}C=true;for(var F;F=B.shift();F()){}};if(document.addEventListener){document.addEventListener("DOMContentLoaded",D,false);window.addEventListener("pageshow",D,false)}if(!window.opera&&document.readyState){(function(){E[document.readyState]?D():setTimeout(arguments.callee,10)})()}if(document.readyState&&document.createStyleSheet){(function(){try{document.body.doScroll("left");D()}catch(F){setTimeout(arguments.callee,1)}})()}q(window,"load",D);return function(F){if(!arguments.length){D()}else{C?F():B.push(F)}}})(),root:function(){return document.documentElement||document.body}};var n=m.CSS={Size:function(C,B){this.value=parseFloat(C);this.unit=String(C).match(/[a-z%]*$/)[0]||"px";this.convert=function(D){return D/B*this.value};this.convertFrom=function(D){return D/this.value*B};this.toString=function(){return this.value+this.unit}},addClass:function(C,B){var D=C.className;C.className=D+(D&&" ")+B;return C},color:j(function(C){var B={};B.color=C.replace(/^rgba\((.*?),\s*([\d.]+)\)/,function(E,D,F){B.opacity=parseFloat(F);return"rgb("+D+")"});return B}),fontStretch:j(function(B){if(typeof B=="number"){return B}if(/%$/.test(B)){return parseFloat(B)/100}return{"ultra-condensed":0.5,"extra-condensed":0.625,condensed:0.75,"semi-condensed":0.875,"semi-expanded":1.125,expanded:1.25,"extra-expanded":1.5,"ultra-expanded":2}[B]||1}),getStyle:function(C){var B=document.defaultView;if(B&&B.getComputedStyle){return new a(B.getComputedStyle(C,null))}if(C.currentStyle){return new a(C.currentStyle)}return new a(C.style)},gradient:j(function(F){var G={id:F,type:F.match(/^-([a-z]+)-gradient\(/)[1],stops:[]},C=F.substr(F.indexOf("(")).match(/([\d.]+=)?(#[a-f0-9]+|[a-z]+\(.*?\)|[a-z]+)/ig);for(var E=0,B=C.length,D;E<B;++E){D=C[E].split("=",2).reverse();G.stops.push([D[1]||E/(B-1),D[0]])}return G}),quotedList:j(function(E){var D=[],C=/\s*((["'])([\s\S]*?[^\\])\2|[^,]+)\s*/g,B;while(B=C.exec(E)){D.push(B[3]||B[1])}return D}),recognizesMedia:j(function(G){var E=document.createElement("style"),D,C,B;E.type="text/css";E.media=G;try{E.appendChild(document.createTextNode("/**/"))}catch(F){}C=g("head")[0];C.insertBefore(E,C.firstChild);D=(E.sheet||E.styleSheet);B=D&&!D.disabled;C.removeChild(E);return B}),removeClass:function(D,C){var B=RegExp("(?:^|\\s+)"+C+"(?=\\s|$)","g");D.className=D.className.replace(B,"");return D},supports:function(D,C){var B=document.createElement("span").style;if(B[D]===undefined){return false}B[D]=C;return B[D]===C},textAlign:function(E,D,B,C){if(D.get("textAlign")=="right"){if(B>0){E=" "+E}}else{if(B<C-1){E+=" "}}return E},textShadow:j(function(F){if(F=="none"){return null}var E=[],G={},B,C=0;var D=/(#[a-f0-9]+|[a-z]+\(.*?\)|[a-z]+)|(-?[\d.]+[a-z%]*)|,/ig;while(B=D.exec(F)){if(B[0]==","){E.push(G);G={};C=0}else{if(B[1]){G.color=B[1]}else{G[["offX","offY","blur"][C++]]=B[2]}}}E.push(G);return E}),textTransform:(function(){var B={uppercase:function(C){return C.toUpperCase()},lowercase:function(C){return C.toLowerCase()},capitalize:function(C){return C.replace(/\b./g,function(D){return D.toUpperCase()})}};return function(E,D){var C=B[D.get("textTransform")];return C?C(E):E}})(),whiteSpace:(function(){var D={inline:1,"inline-block":1,"run-in":1};var C=/^\s+/,B=/\s+$/;return function(H,F,G,E){if(E){if(E.nodeName.toLowerCase()=="br"){H=H.replace(C,"")}}if(D[F.get("display")]){return H}if(!G.previousSibling){H=H.replace(C,"")}if(!G.nextSibling){H=H.replace(B,"")}return H}})()};n.ready=(function(){var B=!n.recognizesMedia("all"),E=false;var D=[],H=function(){B=true;for(var K;K=D.shift();K()){}};var I=g("link"),J=g("style");function C(K){return K.disabled||G(K.sheet,K.media||"screen")}function G(M,P){if(!n.recognizesMedia(P||"all")){return true}if(!M||M.disabled){return false}try{var Q=M.cssRules,O;if(Q){search:for(var L=0,K=Q.length;O=Q[L],L<K;++L){switch(O.type){case 2:break;case 3:if(!G(O.styleSheet,O.media.mediaText)){return false}break;default:break search}}}}catch(N){}return true}function F(){if(document.createStyleSheet){return true}var L,K;for(K=0;L=I[K];++K){if(L.rel.toLowerCase()=="stylesheet"&&!C(L)){return false}}for(K=0;L=J[K];++K){if(!C(L)){return false}}return true}x.ready(function(){if(!E){E=n.getStyle(document.body).isUsable()}if(B||(E&&F())){H()}else{setTimeout(arguments.callee,10)}});return function(K){if(B){K()}else{D.push(K)}}})();function s(D){var C=this.face=D.face,B={"\u0020":1,"\u00a0":1,"\u3000":1};this.glyphs=D.glyphs;this.w=D.w;this.baseSize=parseInt(C["units-per-em"],10);this.family=C["font-family"].toLowerCase();this.weight=C["font-weight"];this.style=C["font-style"]||"normal";this.viewBox=(function(){var F=C.bbox.split(/\s+/);var E={minX:parseInt(F[0],10),minY:parseInt(F[1],10),maxX:parseInt(F[2],10),maxY:parseInt(F[3],10)};E.width=E.maxX-E.minX;E.height=E.maxY-E.minY;E.toString=function(){return[this.minX,this.minY,this.width,this.height].join(" ")};return E})();this.ascent=-parseInt(C.ascent,10);this.descent=-parseInt(C.descent,10);this.height=-this.ascent+this.descent;this.spacing=function(L,N,E){var O=this.glyphs,M,K,G,P=[],F=0,J=-1,I=-1,H;while(H=L[++J]){M=O[H]||this.missingGlyph;if(!M){continue}if(K){F-=G=K[H]||0;P[I]-=G}F+=P[++I]=~~(M.w||this.w)+N+(B[H]?E:0);K=M.k}P.total=F;return P}}function f(){var C={},B={oblique:"italic",italic:"oblique"};this.add=function(D){(C[D.style]||(C[D.style]={}))[D.weight]=D};this.get=function(H,I){var G=C[H]||C[B[H]]||C.normal||C.italic||C.oblique;if(!G){return null}I={normal:400,bold:700}[I]||parseInt(I,10);if(G[I]){return G[I]}var E={1:1,99:0}[I%100],K=[],F,D;if(E===undefined){E=I>400}if(I==500){I=400}for(var J in G){if(!k(G,J)){continue}J=parseInt(J,10);if(!F||J<F){F=J}if(!D||J>D){D=J}K.push(J)}if(I<F){I=F}if(I>D){I=D}K.sort(function(M,L){return(E?(M>=I&&L>=I)?M<L:M>L:(M<=I&&L<=I)?M>L:M<L)?-1:1});return G[K[0]]}}function r(){function D(F,G){if(F.contains){return F.contains(G)}return F.compareDocumentPosition(G)&16}function B(G){var F=G.relatedTarget;if(!F||D(this,F)){return}C(this,G.type=="mouseover")}function E(F){C(this,F.type=="mouseenter")}function C(F,G){setTimeout(function(){var H=d.get(F).options;m.replace(F,G?h(H,H.hover):H,true)},10)}this.attach=function(F){if(F.onmouseenter===undefined){q(F,"mouseover",B);q(F,"mouseout",B)}else{q(F,"mouseenter",E);q(F,"mouseleave",E)}}}function u(){var C=[],D={};function B(H){var E=[],G;for(var F=0;G=H[F];++F){E[F]=C[D[G]]}return E}this.add=function(F,E){D[F]=C.push(E)-1};this.repeat=function(){var E=arguments.length?B(arguments):C,F;for(var G=0;F=E[G++];){m.replace(F[0],F[1],true)}}}function A(){var D={},B=0;function C(E){return E.cufid||(E.cufid=++B)}this.get=function(E){var F=C(E);return D[F]||(D[F]={})}}function a(B){var D={},C={};this.extend=function(E){for(var F in E){if(k(E,F)){D[F]=E[F]}}return this};this.get=function(E){return D[E]!=undefined?D[E]:B[E]};this.getSize=function(F,E){return C[F]||(C[F]=new n.Size(this.get(F),E))};this.isUsable=function(){return!!B}}function q(C,B,D){if(C.addEventListener){C.addEventListener(B,D,false)}else{if(C.attachEvent){C.attachEvent("on"+B,function(){return D.call(C,window.event)})}}}function v(C,B){var D=d.get(C);if(D.options){return C}if(B.hover&&B.hoverables[C.nodeName.toLowerCase()]){b.attach(C)}D.options=B;return C}function j(B){var C={};return function(D){if(!k(C,D)){C[D]=B.apply(null,arguments)}return C[D]}}function c(F,E){var B=n.quotedList(E.get("fontFamily").toLowerCase()),D;for(var C=0;D=B[C];++C){if(i[D]){return i[D].get(E.get("fontStyle"),E.get("fontWeight"))}}return null}function g(B){return document.getElementsByTagName(B)}function k(C,B){return C.hasOwnProperty(B)}function h(){var C={},B,F;for(var E=0,D=arguments.length;B=arguments[E],E<D;++E){for(F in B){if(k(B,F)){C[F]=B[F]}}}return C}function o(E,M,C,N,F,D){var K=document.createDocumentFragment(),H;if(M===""){return K}var L=N.separate;var I=M.split(p[L]),B=(L=="words");if(B&&t){if(/^\s/.test(M)){I.unshift("")}if(/\s$/.test(M)){I.push("")}}for(var J=0,G=I.length;J<G;++J){H=z[N.engine](E,B?n.textAlign(I[J],C,J,G):I[J],C,N,F,D,J<G-1);if(H){K.appendChild(H)}}return K}function l(D,M){var C=D.nodeName.toLowerCase();if(M.ignore[C]){return}var E=!M.textless[C];var B=n.getStyle(v(D,M)).extend(M);var F=c(D,B),G,K,I,H,L,J;if(!F){return}for(G=D.firstChild;G;G=I){K=G.nodeType;I=G.nextSibling;if(E&&K==3){if(H){H.appendData(G.data);D.removeChild(G)}else{H=G}if(I){continue}}if(H){D.replaceChild(o(F,n.whiteSpace(H.data,B,H,J),B,M,G,D),H);H=null}if(K==1){if(G.firstChild){if(G.nodeName.toLowerCase()=="cufon"){z[M.engine](F,null,B,M,G,D)}else{arguments.callee(G,M)}}J=G}}}var t=" ".split(/\s+/).length==0;var d=new A();var b=new r();var y=new u();var e=false;var z={},i={},w={autoDetect:false,engine:null,forceHitArea:false,hover:false,hoverables:{a:true},ignore:{applet:1,canvas:1,col:1,colgroup:1,head:1,iframe:1,map:1,optgroup:1,option:1,script:1,select:1,style:1,textarea:1,title:1,pre:1},printable:true,selector:(window.Sizzle||(window.jQuery&&function(B){return jQuery(B)})||(window.dojo&&dojo.query)||(window.Ext&&Ext.query)||(window.YAHOO&&YAHOO.util&&YAHOO.util.Selector&&YAHOO.util.Selector.query)||(window.$$&&function(B){return $$(B)})||(window.$&&function(B){return $(B)})||(document.querySelectorAll&&function(B){return document.querySelectorAll(B)})||g),separate:"words",textless:{dl:1,html:1,ol:1,table:1,tbody:1,thead:1,tfoot:1,tr:1,ul:1},textShadow:"none"};var p={words:/\s/.test("\u00a0")?/[^\S\u00a0]+/:/\s+/,characters:"",none:/^/};m.now=function(){x.ready();return m};m.refresh=function(){y.repeat.apply(y,arguments);return m};m.registerEngine=function(C,B){if(!B){return m}z[C]=B;return m.set("engine",C)};m.registerFont=function(D){if(!D){return m}var B=new s(D),C=B.family;if(!i[C]){i[C]=new f()}i[C].add(B);return m.set("fontFamily",'"'+C+'"')};m.replace=function(D,C,B){C=h(w,C);if(!C.engine){return m}if(!e){n.addClass(x.root(),"cufon-active cufon-loading");n.ready(function(){n.addClass(n.removeClass(x.root(),"cufon-loading"),"cufon-ready")});e=true}if(C.hover){C.forceHitArea=true}if(C.autoDetect){delete C.fontFamily}if(typeof C.textShadow=="string"){C.textShadow=n.textShadow(C.textShadow)}if(typeof C.color=="string"&&/^-/.test(C.color)){C.textGradient=n.gradient(C.color)}else{delete C.textGradient}if(!B){y.add(D,arguments)}if(D.nodeType||typeof D=="string"){D=[D]}n.ready(function(){for(var F=0,E=D.length;F<E;++F){var G=D[F];if(typeof G=="string"){m.replace(C.selector(G),C,true)}else{l(G,C)}}});return m};m.set=function(B,C){w[B]=C;return m};return m})();Cufon.registerEngine("vml",(function(){var e=document.namespaces;if(!e){return}e.add("cvml","urn:schemas-microsoft-com:vml");e=null;var b=document.createElement("cvml:shape");b.style.behavior="url(#default#VML)";if(!b.coordsize){return}b=null;var h=(document.documentMode||0)<8;document.write(('<style type="text/css">cufoncanvas{text-indent:0;}@media screen{cvml\\:shape,cvml\\:rect,cvml\\:fill,cvml\\:shadow{behavior:url(#default#VML);display:block;antialias:true;position:absolute;}cufoncanvas{position:absolute;text-align:left;}cufon{display:inline-block;position:relative;vertical-align:'+(h?"middle":"text-bottom")+";}cufon cufontext{position:absolute;left:-10000in;font-size:1px;}a cufon{cursor:pointer}}@media print{cufon cufoncanvas{display:none;}}</style>").replace(/;/g,"!important;"));function c(i,j){return a(i,/(?:em|ex|%)$|^[a-z-]+$/i.test(j)?"1em":j)}function a(l,m){if(m==="0"){return 0}if(/px$/i.test(m)){return parseFloat(m)}var k=l.style.left,j=l.runtimeStyle.left;l.runtimeStyle.left=l.currentStyle.left;l.style.left=m.replace("%","em");var i=l.style.pixelLeft;l.style.left=k;l.runtimeStyle.left=j;return i}function f(l,k,j,n){var i="computed"+n,m=k[i];if(isNaN(m)){m=k.get(n);k[i]=m=(m=="normal")?0:~~j.convertFrom(a(l,m))}return m}var g={};function d(p){var q=p.id;if(!g[q]){var n=p.stops,o=document.createElement("cvml:fill"),i=[];o.type="gradient";o.angle=180;o.focus="0";o.method="sigma";o.color=n[0][1];for(var m=1,l=n.length-1;m<l;++m){i.push(n[m][0]*100+"% "+n[m][1])}o.colors=i.join(",");o.color2=n[l][1];g[q]=o}return g[q]}return function(ac,G,Y,C,K,ad,W){var n=(G===null);if(n){G=K.alt}var I=ac.viewBox;var p=Y.computedFontSize||(Y.computedFontSize=new Cufon.CSS.Size(c(ad,Y.get("fontSize"))+"px",ac.baseSize));var y,q;if(n){y=K;q=K.firstChild}else{y=document.createElement("cufon");y.className="cufon cufon-vml";y.alt=G;q=document.createElement("cufoncanvas");y.appendChild(q);if(C.printable){var Z=document.createElement("cufontext");Z.appendChild(document.createTextNode(G));y.appendChild(Z)}if(!W){y.appendChild(document.createElement("cvml:shape"))}}var ai=y.style;var R=q.style;var l=p.convert(I.height),af=Math.ceil(l);var V=af/l;var P=V*Cufon.CSS.fontStretch(Y.get("fontStretch"));var U=I.minX,T=I.minY;R.height=af;R.top=Math.round(p.convert(T-ac.ascent));R.left=Math.round(p.convert(U));ai.height=p.convert(ac.height)+"px";var F=Y.get("color");var ag=Cufon.CSS.textTransform(G,Y).split("");var L=ac.spacing(ag,f(ad,Y,p,"letterSpacing"),f(ad,Y,p,"wordSpacing"));if(!L.length){return null}var k=L.total;var x=-U+k+(I.width-L[L.length-1]);var ah=p.convert(x*P),X=Math.round(ah);var O=x+","+I.height,m;var J="r"+O+"ns";var u=C.textGradient&&d(C.textGradient);var o=ac.glyphs,S=0;var H=C.textShadow;var ab=-1,aa=0,w;while(w=ag[++ab]){var D=o[ag[ab]]||ac.missingGlyph,v;if(!D){continue}if(n){v=q.childNodes[aa];while(v.firstChild){v.removeChild(v.firstChild)}}else{v=document.createElement("cvml:shape");q.appendChild(v)}v.stroked="f";v.coordsize=O;v.coordorigin=m=(U-S)+","+T;v.path=(D.d?"m"+D.d+"xe":"")+"m"+m+J;v.fillcolor=F;if(u){v.appendChild(u.cloneNode(false))}var ae=v.style;ae.width=X;ae.height=af;if(H){var s=H[0],r=H[1];var B=Cufon.CSS.color(s.color),z;var N=document.createElement("cvml:shadow");N.on="t";N.color=B.color;N.offset=s.offX+","+s.offY;if(r){z=Cufon.CSS.color(r.color);N.type="double";N.color2=z.color;N.offset2=r.offX+","+r.offY}N.opacity=B.opacity||(z&&z.opacity)||1;v.appendChild(N)}S+=L[aa++]}var M=v.nextSibling,t,A;if(C.forceHitArea){if(!M){M=document.createElement("cvml:rect");M.stroked="f";M.className="cufon-vml-cover";t=document.createElement("cvml:fill");t.opacity=0;M.appendChild(t);q.appendChild(M)}A=M.style;A.width=X;A.height=af}else{if(M){q.removeChild(M)}}ai.width=Math.max(Math.ceil(p.convert(k*P)),0);if(h){var Q=Y.computedYAdjust;if(Q===undefined){var E=Y.get("lineHeight");if(E=="normal"){E="1em"}else{if(!isNaN(E)){E+="em"}}Y.computedYAdjust=Q=0.5*(a(ad,E)-parseFloat(ai.height))}if(Q){ai.marginTop=Math.ceil(Q)+"px";ai.marginBottom=Q+"px"}}return y}})());Cufon.registerEngine("canvas",(function(){var b=document.createElement("canvas");if(!b||!b.getContext||!b.getContext.apply){return}b=null;var a=Cufon.CSS.supports("display","inline-block");var e=!a&&(document.compatMode=="BackCompat"||/frameset|transitional/i.test(document.doctype.publicId));var f=document.createElement("style");f.type="text/css";f.appendChild(document.createTextNode(("cufon{text-indent:0;}@media screen,projection{cufon{display:inline;display:inline-block;position:relative;vertical-align:middle;"+(e?"":"font-size:1px;line-height:1px;")+"}cufon cufontext{display:-moz-inline-box;display:inline-block;width:0;height:0;overflow:hidden;text-indent:-10000in;}"+(a?"cufon canvas{position:relative;}":"cufon canvas{position:absolute;}")+"}@media print{cufon{padding:0;}cufon canvas{display:none;}}").replace(/;/g,"!important;")));document.getElementsByTagName("head")[0].appendChild(f);function d(p,h){var n=0,m=0;var g=[],o=/([mrvxe])([^a-z]*)/g,k;generate:for(var j=0;k=o.exec(p);++j){var l=k[2].split(",");switch(k[1]){case"v":g[j]={m:"bezierCurveTo",a:[n+~~l[0],m+~~l[1],n+~~l[2],m+~~l[3],n+=~~l[4],m+=~~l[5]]};break;case"r":g[j]={m:"lineTo",a:[n+=~~l[0],m+=~~l[1]]};break;case"m":g[j]={m:"moveTo",a:[n=~~l[0],m=~~l[1]]};break;case"x":g[j]={m:"closePath"};break;case"e":break generate}h[g[j].m].apply(h,g[j].a)}return g}function c(m,k){for(var j=0,h=m.length;j<h;++j){var g=m[j];k[g.m].apply(k,g.a)}}return function(V,w,P,t,C,W){var k=(w===null);if(k){w=C.getAttribute("alt")}var A=V.viewBox;var m=P.getSize("fontSize",V.baseSize);var B=0,O=0,N=0,u=0;var z=t.textShadow,L=[];if(z){for(var U=z.length;U--;){var F=z[U];var K=m.convertFrom(parseFloat(F.offX));var I=m.convertFrom(parseFloat(F.offY));L[U]=[K,I];if(I<B){B=I}if(K>O){O=K}if(I>N){N=I}if(K<u){u=K}}}var Z=Cufon.CSS.textTransform(w,P).split("");var E=V.spacing(Z,~~m.convertFrom(parseFloat(P.get("letterSpacing"))||0),~~m.convertFrom(parseFloat(P.get("wordSpacing"))||0));if(!E.length){return null}var h=E.total;O+=A.width-E[E.length-1];u+=A.minX;var s,n;if(k){s=C;n=C.firstChild}else{s=document.createElement("cufon");s.className="cufon cufon-canvas";s.setAttribute("alt",w);n=document.createElement("canvas");s.appendChild(n);if(t.printable){var S=document.createElement("cufontext");S.appendChild(document.createTextNode(w));s.appendChild(S)}}var aa=s.style;var H=n.style;var j=m.convert(A.height);var Y=Math.ceil(j);var M=Y/j;var G=M*Cufon.CSS.fontStretch(P.get("fontStretch"));var J=h*G;var Q=Math.ceil(m.convert(J+O-u));var o=Math.ceil(m.convert(A.height-B+N));n.width=Q;n.height=o;H.width=Q+"px";H.height=o+"px";B+=A.minY;H.top=Math.round(m.convert(B-V.ascent))+"px";H.left=Math.round(m.convert(u))+"px";var r=Math.max(Math.ceil(m.convert(J)),0)+"px";if(a){aa.width=r;aa.height=m.convert(V.height)+"px"}else{aa.paddingLeft=r;aa.paddingBottom=(m.convert(V.height)-1)+"px"}var X=n.getContext("2d"),D=j/A.height;X.scale(D,D*M);X.translate(-u,-B);X.save();function T(){var x=V.glyphs,ab,l=-1,g=-1,y;X.scale(G,1);while(y=Z[++l]){var ab=x[Z[l]]||V.missingGlyph;if(!ab){continue}if(ab.d){X.beginPath();if(ab.code){c(ab.code,X)}else{ab.code=d("m"+ab.d,X)}X.fill()}X.translate(E[++g],0)}X.restore()}if(z){for(var U=z.length;U--;){var F=z[U];X.save();X.fillStyle=F.color;X.translate.apply(X,L[U]);T()}}var q=t.textGradient;if(q){var v=q.stops,p=X.createLinearGradient(0,A.minY,0,A.maxY);for(var U=0,R=v.length;U<R;++U){p.addColorStop.apply(p,v[U])}X.fillStyle=p}else{X.fillStyle=P.get("color")}T();return s}})());
;/*
 * The following copyright notice may not be removed under any circumstances.
 * 
 * Copyright:
 * Copyright � 2000 Adobe Systems Incorporated. All Rights Reserved. U.S. Patent
 * Des. pending.
 * 
 * Trademark:
 * Myriad is a registered trademark of Adobe Systems Incorporated.
 * 
 * Full name:
 * MyriadPro-Semibold
 * 
 * Designer:
 * Robert Slimbach and Carol Twombly
 * 
 * Vendor URL:
 * http://www.adobe.com/type
 * 
 * License information:
 * http://www.adobe.com/type/legal.html
 */
Cufon.registerFont({"w":192,"face":{"font-family":"Myriad Pro","font-weight":600,"font-stretch":"normal","units-per-em":"360","panose-1":"2 11 6 3 3 4 3 2 2 4","ascent":"270","descent":"-90","bbox":"0 -270 292 90","underline-thickness":"18","underline-position":"-18","stemh":"33","stemv":"44","unicode-range":"U+0020-U+007E"},"glyphs":{" ":{"w":74},"\u00a0":{"w":74},"!":{"d":"62,-75r-34,0r-6,-168r46,0xm45,4v-15,0,-27,-12,-27,-28v0,-17,11,-28,27,-28v16,0,27,11,27,28v0,16,-10,28,-27,28","w":90},"\"":{"d":"15,-249r41,0r-7,92r-27,0xm77,-249r41,0r-7,92r-26,0","w":133,"k":{",":43,".":43}},"#":{"d":"75,-95r34,0r7,-46r-35,0xm61,0r-28,0r10,-67r-30,0r0,-28r35,0r6,-46r-31,0r0,-27r35,0r9,-66r27,0r-9,66r35,0r9,-66r27,0r-9,66r29,0r0,27r-33,0r-6,46r30,0r0,28r-35,0r-9,67r-27,0r9,-67r-35,0","w":189},"$":{"d":"109,31r-29,0r0,-35v-23,-1,-45,-8,-58,-16r9,-34v22,17,94,28,95,-12v0,-17,-13,-27,-40,-37v-37,-14,-62,-31,-62,-64v0,-31,22,-55,58,-61r0,-35r30,0r0,33v23,1,38,6,49,12r-9,33v-12,-13,-84,-20,-84,13v0,15,12,26,44,35v79,24,77,116,-3,131r0,37"},"%":{"d":"72,-238v36,0,59,28,59,70v0,49,-29,74,-61,74v-33,0,-60,-26,-60,-71v0,-44,26,-73,62,-73xm97,-166v0,-26,-7,-48,-27,-47v-18,0,-26,20,-26,47v0,27,10,47,27,47v18,0,26,-18,26,-47xm95,4r-25,0r136,-242r25,0xm233,-141v37,0,59,28,59,70v0,49,-29,74,-61,74v-33,0,-60,-26,-60,-71v0,-44,26,-73,62,-73xm232,-116v-18,0,-27,21,-27,47v0,27,9,47,27,47v18,0,26,-19,26,-47v0,-26,-7,-47,-26,-47","w":302},"&":{"d":"230,0r-51,0r-20,-20v-18,15,-40,24,-68,24v-98,1,-101,-110,-33,-139v-35,-41,-21,-111,48,-112v34,0,61,22,61,55v0,27,-20,44,-51,65r49,55v10,-16,18,-38,22,-64r40,0v-6,37,-18,67,-38,90xm53,-72v0,43,62,55,86,27v-15,-15,-38,-41,-61,-67v-12,8,-25,20,-25,40xm104,-218v-37,0,-29,48,-8,68v21,-13,33,-24,33,-41v0,-13,-8,-27,-25,-27","w":232},"(":{"d":"66,-249r32,0v-51,59,-52,231,0,291r-32,0v-21,-29,-44,-76,-44,-145v0,-71,23,-117,44,-146","w":108},")":{"d":"42,42r-32,0v51,-60,52,-231,0,-291r32,0v21,28,44,74,44,145v0,70,-23,116,-44,146","w":108},"*":{"d":"100,-247r27,16r-37,46r57,-9r0,31v-18,-2,-40,-8,-57,-8r37,44r-28,16r-21,-54r-22,54r-26,-16r36,-45r-55,9r0,-31v18,2,39,8,55,8r-36,-45r27,-16v8,17,13,37,22,53","w":157},"+":{"d":"93,-192r29,0r0,82r79,0r0,28r-79,0r0,82r-29,0r0,-82r-79,0r0,-28r79,0r0,-82","w":214},",":{"d":"33,41r-30,3v10,-27,19,-65,24,-95r46,-4v-10,34,-26,73,-40,96","w":84,"k":{"\"":41,"'":41}},"-":{"d":"11,-111r92,0r0,30r-92,0r0,-30","w":113},"\u00ad":{"d":"11,-111r92,0r0,30r-92,0r0,-30","w":113},".":{"d":"73,-24v0,16,-10,28,-28,28v-16,0,-27,-12,-27,-28v0,-17,12,-29,28,-29v16,0,27,12,27,29","w":84,"k":{"\"":41,"'":41}},"\/":{"d":"34,14r-31,0r88,-261r31,0","w":121},"0":{"d":"95,4v-54,0,-83,-49,-83,-120v0,-73,31,-122,86,-122v57,0,83,50,83,119v0,76,-30,123,-86,123xm96,-30v27,0,40,-32,40,-88v0,-54,-12,-86,-39,-86v-24,0,-40,31,-40,86v0,57,15,88,39,88"},"1":{"d":"85,0r-1,-194r-43,22r-7,-34v29,-12,48,-33,94,-28r0,234r-43,0"},"2":{"d":"174,0r-159,0r0,-27v42,-40,119,-104,110,-135v0,-21,-11,-40,-42,-40v-21,0,-38,10,-50,19r-13,-31v17,-14,42,-24,72,-24v52,0,77,33,77,71v0,48,-53,94,-91,131r96,0r0,36"},"3":{"d":"14,-12r10,-34v9,5,32,15,54,15v34,0,47,-20,47,-38v-1,-35,-36,-41,-73,-39r0,-33v32,2,63,-2,66,-32v4,-37,-68,-33,-86,-15r-10,-32v13,-9,40,-18,68,-18v85,-3,97,89,30,112v26,8,51,25,51,59v0,40,-33,71,-91,71v-28,0,-53,-8,-66,-16"},"4":{"d":"155,0r-42,0r0,-59r-106,0r0,-29r96,-146r52,0r0,141r30,0r0,34r-30,0r0,59xm49,-94v18,3,44,0,64,1r0,-104v-17,40,-41,69,-64,103"},"5":{"d":"68,-150v54,-8,104,18,104,73v0,45,-38,81,-95,81v-27,0,-50,-7,-62,-14r9,-33v29,17,105,21,102,-30v7,-37,-53,-51,-98,-43r15,-118r122,0r0,37r-90,0"},"6":{"d":"158,-237r0,35v-57,-3,-100,33,-100,69v39,-44,124,-22,124,53v0,45,-32,84,-82,84v-105,0,-106,-156,-48,-206v27,-23,61,-35,106,-35xm96,-123v-21,1,-41,14,-41,38v0,31,15,56,45,56v23,0,37,-20,37,-48v0,-27,-15,-46,-41,-46"},"7":{"d":"19,-234r157,0r0,28r-98,206r-47,0r99,-198r-111,0r0,-36"},"8":{"d":"56,-121v-63,-33,-33,-120,43,-117v84,4,95,80,37,113v26,10,45,30,45,59v0,42,-36,70,-86,70v-95,0,-108,-98,-39,-125xm92,-106v-47,9,-45,79,5,79v23,0,39,-15,39,-35v0,-24,-18,-37,-44,-44xm101,-139v36,-6,42,-68,-5,-68v-22,0,-33,14,-33,31v0,19,16,31,38,37"},"9":{"d":"33,3r0,-35v56,6,94,-24,102,-71v-38,43,-122,16,-122,-51v0,-45,34,-84,84,-84v103,0,100,163,43,208v-28,22,-61,36,-107,33xm57,-157v0,50,78,59,78,11v0,-32,-12,-59,-40,-59v-22,0,-38,20,-38,48"},":":{"d":"45,-117v-15,0,-27,-12,-27,-28v0,-17,12,-29,28,-29v16,0,27,12,27,29v0,16,-11,28,-28,28xm45,4v-15,0,-27,-12,-27,-28v0,-17,12,-29,28,-29v16,0,27,12,27,29v0,16,-11,28,-28,28","w":84},";":{"d":"33,41r-30,3v10,-27,19,-65,24,-95r46,-4v-10,34,-26,73,-40,96xm48,-117v-15,0,-27,-12,-27,-28v0,-17,11,-29,27,-29v16,0,27,12,27,29v0,16,-10,28,-27,28","w":84},"<":{"d":"22,-82r0,-26r170,-83r0,32r-135,64r135,63r0,32","w":214},"=":{"d":"200,-119r-186,0r0,-28r186,0r0,28xm200,-46r-186,0r0,-28r186,0r0,28","w":214},">":{"d":"193,-109r0,27r-171,82r0,-32r137,-64r-137,-63r0,-32","w":214},"?":{"d":"139,-192v1,47,-58,67,-50,118r-39,0v-12,-46,41,-78,44,-112v2,-30,-47,-30,-66,-15r-10,-31v13,-8,33,-15,56,-15v45,0,65,26,65,55xm96,-24v0,16,-10,28,-28,28v-16,0,-27,-12,-27,-28v0,-17,12,-28,28,-28v16,0,27,11,27,28","w":154},"@":{"d":"123,-48v30,-2,40,-48,44,-80v-36,-10,-64,19,-64,55v0,15,6,25,20,25xm186,5r7,19v-81,37,-177,-5,-176,-100v0,-74,52,-137,133,-137v63,0,107,44,107,104v0,53,-30,86,-67,86v-16,0,-25,-13,-29,-30v-21,41,-87,42,-89,-17v-2,-58,65,-103,126,-76r-12,66v-4,24,0,35,11,35v17,1,36,-21,36,-63v0,-49,-30,-85,-86,-85v-57,0,-106,44,-106,115v0,81,79,115,145,83","w":271},"[":{"d":"97,40r-70,0r0,-287r70,0r0,26r-38,0r0,236r38,0r0,25","w":108},"\\":{"d":"118,14r-31,0r-84,-261r30,0","w":120},"]":{"d":"11,-247r70,0r0,287r-70,0r0,-25r38,0r0,-236r-38,0r0,-26","w":108},"^":{"d":"197,-66r-33,0r-57,-133r-56,133r-33,0r75,-168r29,0","w":214},"_":{"d":"0,27r180,0r0,18r-180,0r0,-18","w":180},"{":{"d":"34,-9v-1,-35,25,-79,-24,-83r0,-23v50,-2,23,-50,24,-84v2,-37,26,-50,64,-48r0,26v-68,-7,6,109,-56,118v36,3,27,54,24,89v-2,21,10,31,32,29r0,25v-38,2,-63,-9,-64,-49","w":108},"|":{"d":"31,-270r32,0r0,360r-32,0r0,-360","w":94},"}":{"d":"74,-199v1,35,-25,81,24,84r0,23v-50,4,-23,48,-24,83v-2,40,-26,51,-64,49r0,-25v68,7,-6,-110,56,-119v-36,-3,-28,-54,-24,-89v2,-21,-10,-30,-32,-28r0,-26v38,-2,63,11,64,48","w":108},"~":{"d":"153,-70v-23,0,-68,-31,-89,-32v-11,0,-18,8,-19,30r-28,0v-1,-42,19,-62,46,-62v25,0,67,32,90,32v10,0,16,-9,17,-30r28,0v2,46,-20,62,-45,62","w":214},"'":{"d":"15,-249r41,0r-7,92r-27,0","w":71,"k":{",":43,".":43}},"`":{"d":"4,-251r44,0r31,53r-31,0","w":108}}});
;;(function($){var tmp,loading,overlay,wrap,outer,content,close,title,nav_left,nav_right,selectedIndex=0,selectedOpts={},selectedArray=[],currentIndex=0,currentOpts={},currentArray=[],ajaxLoader=null,imgPreloader=new Image(),imgRegExp=/\.(jpg|gif|png|bmp|jpeg)(.*)?$/i,swfRegExp=/[^\.]\.(swf)\s*$/i,loadingTimer,loadingFrame=1,titleHeight=0,titleStr='',start_pos,final_pos,busy=false,fx=$.extend($('<div/>')[0],{prop:0}),isIE6=$.browser.msie&&$.browser.version<7&&!window.XMLHttpRequest,_abort=function(){loading.hide();overlay.hide();imgPreloader.onerror=imgPreloader.onload=null;if(ajaxLoader){ajaxLoader.abort();}
tmp.empty();$('#support_form').css('visibility','visible');$('#support_form_overlay').remove();$('#ticket_sent').remove();},_error=function(){if(false===selectedOpts.onError(selectedArray,selectedIndex,selectedOpts)){loading.hide();busy=false;return;}
selectedOpts.titleShow=false;selectedOpts.width='auto';selectedOpts.height='auto';tmp.html('<p id="fancybox-error">The requested content cannot be loaded.<br />Please try again later.</p>');_process_inline();},_start=function(){var obj=selectedArray[selectedIndex],href,type,title,str,emb,ret;_abort();selectedOpts=$.extend({},$.fn.fancybox.defaults,(typeof $(obj).data('fancybox')=='undefined'?selectedOpts:$(obj).data('fancybox')));ret=selectedOpts.onStart(selectedArray,selectedIndex,selectedOpts);if(ret===false){busy=false;return;}else if(typeof ret=='object'){selectedOpts=$.extend(selectedOpts,ret);}
title=selectedOpts.title||(obj.nodeName?$(obj).attr('title'):obj.title)||'';if(obj.nodeName&&!selectedOpts.orig){selectedOpts.orig=$(obj).children("img:first").length?$(obj).children("img:first"):$(obj);}
if(title===''&&selectedOpts.orig&&selectedOpts.titleFromAlt){title=selectedOpts.orig.attr('alt');}
href=selectedOpts.href||(obj.nodeName?$(obj).attr('href'):obj.href)||null;if((/^(?:javascript)/i).test(href)||href=='#'){href=null;}
if(selectedOpts.type){type=selectedOpts.type;if(!href){href=selectedOpts.content;}}else if(selectedOpts.content){type='html';}else if(href){if(href.match(imgRegExp)){type='image';}else if(href.match(swfRegExp)){type='swf';}else if($(obj).hasClass("iframe")){type='iframe';}else if(href.indexOf("#")===0){type='inline';}else{type='ajax';}}
if(!type){_error();return;}
if(type=='inline'){obj=href.substr(href.indexOf("#"));type=$(obj).length>0?'inline':'ajax';}
selectedOpts.type=type;selectedOpts.href=href;selectedOpts.title=title;if(selectedOpts.autoDimensions){if(selectedOpts.type=='html'||selectedOpts.type=='inline'||selectedOpts.type=='ajax'){selectedOpts.width='auto';selectedOpts.height='auto';}else{selectedOpts.autoDimensions=false;}}
if(selectedOpts.modal){selectedOpts.overlayShow=true;selectedOpts.hideOnOverlayClick=false;selectedOpts.hideOnContentClick=false;selectedOpts.enableEscapeButton=false;selectedOpts.showCloseButton=false;}
selectedOpts.padding=parseInt(selectedOpts.padding,10);selectedOpts.margin=parseInt(selectedOpts.margin,10);tmp.css('padding',(selectedOpts.padding+selectedOpts.margin));$('.fancybox-inline-tmp').unbind('fancybox-cancel').bind('fancybox-change',function(){$(this).replaceWith(content.children());});currentOpts=selectedOpts;if(currentOpts.overlayShow){overlay.css({'background-color':currentOpts.overlayColor,'opacity':currentOpts.overlayOpacity,'cursor':currentOpts.hideOnOverlayClick?'pointer':'auto','height':$(document).height()});if(!overlay.is(':visible')){if(isIE6){$('select:not(#fancybox-tmp select)').filter(function(){return this.style.visibility!=='hidden';}).css({'visibility':'hidden'}).one('fancybox-cleanup',function(){this.style.visibility='inherit';});}
overlay.show();}}else{overlay.hide();}
switch(type){case'html':tmp.html(selectedOpts.content);_process_inline();break;case'inline':if($(obj).parent().is('#fancybox-content')===true){busy=false;return;}
$('<div class="fancybox-inline-tmp" />').hide().insertBefore($(obj)).bind('fancybox-cleanup',function(){$(this).replaceWith(content.children());}).bind('fancybox-cancel',function(){$(this).replaceWith(tmp.children());});$(obj).appendTo(tmp);_process_inline();break;case'image':busy=false;$.fancybox.showActivity();imgPreloader=new Image();imgPreloader.onerror=function(){_error();};imgPreloader.onload=function(){busy=true;imgPreloader.onerror=imgPreloader.onload=null;_process_image();};imgPreloader.src=href;break;case'swf':selectedOpts.scrolling='no';str='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+selectedOpts.width+'" height="'+selectedOpts.height+'"><param name="movie" value="'+href+'"></param>';emb='';$.each(selectedOpts.swf,function(name,val){str+='<param name="'+name+'" value="'+val+'"></param>';emb+=' '+name+'="'+val+'"';});str+='<embed src="'+href+'" type="application/x-shockwave-flash" width="'+selectedOpts.width+'" height="'+selectedOpts.height+'"'+emb+'></embed></object>';tmp.html(str);_process_inline();break;case'ajax':busy=false;$.fancybox.showActivity();selectedOpts.ajax.win=selectedOpts.ajax.success;ajaxLoader=$.ajax($.extend({},selectedOpts.ajax,{url:href,data:selectedOpts.ajax.data||{},error:function(XMLHttpRequest,textStatus,errorThrown){if(XMLHttpRequest.status>0){_error();}},success:function(data,textStatus,XMLHttpRequest){var o=typeof XMLHttpRequest=='object'?XMLHttpRequest:ajaxLoader;if(o.status==200){if(typeof selectedOpts.ajax.win=='function'){ret=selectedOpts.ajax.win(href,data,textStatus,XMLHttpRequest);if(ret===false){loading.hide();return;}else if(typeof ret=='string'||typeof ret=='object'){data=ret;}}
tmp.html(data);_process_inline();}}}));break;case'iframe':_show();break;}},_process_inline=function(){var
w=selectedOpts.width,h=selectedOpts.height;if(w.toString().indexOf('%')>-1){w=parseInt(($(window).width()-(selectedOpts.margin*2))*parseFloat(w)/100,10)+'px';}else{w=w=='auto'?'auto':w+'px';}
if(h.toString().indexOf('%')>-1){h=parseInt(($(window).height()-(selectedOpts.margin*2))*parseFloat(h)/100,10)+'px';}else{h=h=='auto'?'auto':h+'px';}
tmp.wrapInner('<div style="width:'+w+';height:'+h+';overflow: '+(selectedOpts.scrolling=='auto'?'auto':(selectedOpts.scrolling=='yes'?'scroll':'hidden'))+';position:relative;"></div>');selectedOpts.width=tmp.width();selectedOpts.height=tmp.height();_show();},_process_image=function(){selectedOpts.width=imgPreloader.width;selectedOpts.height=imgPreloader.height;$("<img />").attr({'id':'fancybox-img','src':imgPreloader.src,'alt':selectedOpts.title}).appendTo(tmp);_show();},_show=function(){var pos,equal;loading.hide();if(wrap.is(":visible")&&false===currentOpts.onCleanup(currentArray,currentIndex,currentOpts)){$.event.trigger('fancybox-cancel');busy=false;return;}
busy=true;$(content.add(overlay)).unbind();$(window).unbind("resize.fb scroll.fb");$(document).unbind('keydown.fb');if(wrap.is(":visible")&&currentOpts.titlePosition!=='outside'){wrap.css('height',wrap.height());}
currentArray=selectedArray;currentIndex=selectedIndex;currentOpts=selectedOpts;final_pos=_get_zoom_to();_process_title();if(wrap.is(":visible")){$(close.add(nav_left).add(nav_right)).hide();pos=wrap.position(),start_pos={top:pos.top,left:pos.left,width:wrap.width(),height:wrap.height()};equal=(start_pos.width==final_pos.width&&start_pos.height==final_pos.height);content.fadeTo(currentOpts.changeFade,0.3,function(){var finish_resizing=function(){content.html(tmp.contents()).fadeTo(currentOpts.changeFade,1,_finish);};$.event.trigger('fancybox-change');content.empty().removeAttr('filter').css({'border-width':currentOpts.padding,'width':final_pos.width-currentOpts.padding*2,'height':selectedOpts.autoDimensions?'auto':final_pos.height-titleHeight-currentOpts.padding*2});if(equal){finish_resizing();}else{fx.prop=0;$(fx).animate({prop:1},{duration:currentOpts.changeSpeed,easing:currentOpts.easingChange,step:_draw,complete:finish_resizing});}});return;}
wrap.removeAttr("style");content.css('border-width',currentOpts.padding);if(currentOpts.transitionIn=='elastic'){start_pos=_get_zoom_from();content.html(tmp.contents());wrap.show();if(currentOpts.opacity){final_pos.opacity=0;}
fx.prop=0;$(fx).animate({prop:1},{duration:currentOpts.speedIn,easing:currentOpts.easingIn,step:_draw,complete:_finish});return;}
if(currentOpts.titlePosition=='inside'&&titleHeight>0){title.show();}
content.css({'width':final_pos.width-currentOpts.padding*2,'height':selectedOpts.autoDimensions?'auto':final_pos.height-titleHeight-currentOpts.padding*2}).html(tmp.contents());wrap.css(final_pos).fadeIn(currentOpts.transitionIn=='none'?0:currentOpts.speedIn,_finish);},_format_title=function(title){if(title&&title.length){if(currentOpts.titlePosition=='float'){return'<table id="fancybox-title-float-wrap" cellpadding="0" cellspacing="0"><tr><td id="fancybox-title-float-left"></td><td id="fancybox-title-float-main">'+title+'</td><td id="fancybox-title-float-right"></td></tr></table>';}
return'<div id="fancybox-title-'+currentOpts.titlePosition+'">'+title+'</div>';}
return false;},_process_title=function(){titleStr=currentOpts.title||'';titleHeight=0;title.empty().removeAttr('style').removeClass();if(currentOpts.titleShow===false){title.hide();return;}
titleStr=$.isFunction(currentOpts.titleFormat)?currentOpts.titleFormat(titleStr,currentArray,currentIndex,currentOpts):_format_title(titleStr);if(!titleStr||titleStr===''){title.hide();return;}
title.addClass('fancybox-title-'+currentOpts.titlePosition).html(titleStr).appendTo('body').show();switch(currentOpts.titlePosition){case'inside':title.css({'width':final_pos.width-(currentOpts.padding*2),'marginLeft':currentOpts.padding,'marginRight':currentOpts.padding});titleHeight=title.outerHeight(true);title.appendTo(outer);final_pos.height+=titleHeight;break;case'over':title.css({'marginLeft':currentOpts.padding,'width':final_pos.width-(currentOpts.padding*2),'bottom':currentOpts.padding}).appendTo(outer);break;case'float':title.css('left',parseInt((title.width()-final_pos.width-40)/2,10)*-1).appendTo(wrap);break;default:title.css({'width':final_pos.width-(currentOpts.padding*2),'paddingLeft':currentOpts.padding,'paddingRight':currentOpts.padding}).appendTo(wrap);break;}
title.hide();},_set_navigation=function(){if(currentOpts.enableEscapeButton||currentOpts.enableKeyboardNav){$(document).bind('keydown.fb',function(e){if(e.keyCode==27&&currentOpts.enableEscapeButton){e.preventDefault();$.fancybox.close();}else if((e.keyCode==37||e.keyCode==39)&&currentOpts.enableKeyboardNav&&e.target.tagName!=='INPUT'&&e.target.tagName!=='TEXTAREA'&&e.target.tagName!=='SELECT'){e.preventDefault();$.fancybox[e.keyCode==37?'prev':'next']();}});}
if(!currentOpts.showNavArrows){nav_left.hide();nav_right.hide();return;}
if((currentOpts.cyclic&&currentArray.length>1)||currentIndex!==0){nav_left.show();}
if((currentOpts.cyclic&&currentArray.length>1)||currentIndex!=(currentArray.length-1)){nav_right.show();}},_finish=function(){if(!$.support.opacity){content.get(0).style.removeAttribute('filter');wrap.get(0).style.removeAttribute('filter');}
if(selectedOpts.autoDimensions){content.css('height','auto');}
wrap.css('height','auto');if(titleStr&&titleStr.length){title.show();}
if(currentOpts.showCloseButton){close.show();}
_set_navigation();if(currentOpts.hideOnContentClick){content.bind('click',$.fancybox.close);}
if(currentOpts.hideOnOverlayClick){overlay.bind('click',$.fancybox.close);}
$(window).bind("resize.fb",$.fancybox.resize);if(currentOpts.centerOnScroll){$(window).bind("scroll.fb",$.fancybox.center);}
if(currentOpts.type=='iframe'){$('<iframe id="fancybox-frame" name="fancybox-frame'+new Date().getTime()+'" frameborder="0" hspace="0" '+($.browser.msie?'allowtransparency="true""':'')+' scrolling="'+selectedOpts.scrolling+'" src="'+currentOpts.href+'"></iframe>').appendTo(content);}
wrap.show();busy=false;$.fancybox.center();currentOpts.onComplete(currentArray,currentIndex,currentOpts);_preload_images();},_preload_images=function(){var href,objNext;if((currentArray.length-1)>currentIndex){href=currentArray[currentIndex+1].href;if(typeof href!=='undefined'&&href.match(imgRegExp)){objNext=new Image();objNext.src=href;}}
if(currentIndex>0){href=currentArray[currentIndex-1].href;if(typeof href!=='undefined'&&href.match(imgRegExp)){objNext=new Image();objNext.src=href;}}},_draw=function(pos){var dim={width:parseInt(start_pos.width+(final_pos.width-start_pos.width)*pos,10),height:parseInt(start_pos.height+(final_pos.height-start_pos.height)*pos,10),top:parseInt(start_pos.top+(final_pos.top-start_pos.top)*pos,10),left:parseInt(start_pos.left+(final_pos.left-start_pos.left)*pos,10)};if(typeof final_pos.opacity!=='undefined'){dim.opacity=pos<0.5?0.5:pos;}
wrap.css(dim);content.css({'width':dim.width-currentOpts.padding*2,'height':dim.height-(titleHeight*pos)-currentOpts.padding*2});},_get_viewport=function(){return[$(window).width()-(currentOpts.margin*2),$(window).height()-(currentOpts.margin*2),$(document).scrollLeft()+currentOpts.margin,$(document).scrollTop()+currentOpts.margin];},_get_zoom_to=function(){var view=_get_viewport(),to={},resize=currentOpts.autoScale,double_padding=currentOpts.padding*2,ratio;if(currentOpts.width.toString().indexOf('%')>-1){to.width=parseInt((view[0]*parseFloat(currentOpts.width))/100,10);}else{to.width=currentOpts.width+double_padding;}
if(currentOpts.height.toString().indexOf('%')>-1){to.height=parseInt((view[1]*parseFloat(currentOpts.height))/100,10);}else{to.height=currentOpts.height+double_padding;}
if(resize&&(to.width>view[0]||to.height>view[1])){if(selectedOpts.type=='image'||selectedOpts.type=='swf'){ratio=(currentOpts.width)/(currentOpts.height);if((to.width)>view[0]){to.width=view[0];to.height=parseInt(((to.width-double_padding)/ratio)+double_padding,10);}
if((to.height)>view[1]){to.height=view[1];to.width=parseInt(((to.height-double_padding)*ratio)+double_padding,10);}}else{to.width=Math.min(to.width,view[0]);to.height=Math.min(to.height,view[1]);}}
to.top=parseInt(Math.max(view[3]-20,view[3]+((view[1]-to.height-40)*0.5)),10);to.left=parseInt(Math.max(view[2]-20,view[2]+((view[0]-to.width-40)*0.5)),10);return to;},_get_obj_pos=function(obj){var pos=obj.offset();pos.top+=parseInt(obj.css('paddingTop'),10)||0;pos.left+=parseInt(obj.css('paddingLeft'),10)||0;pos.top+=parseInt(obj.css('border-top-width'),10)||0;pos.left+=parseInt(obj.css('border-left-width'),10)||0;pos.width=obj.width();pos.height=obj.height();return pos;},_get_zoom_from=function(){var orig=selectedOpts.orig?$(selectedOpts.orig):false,from={},pos,view;if(orig&&orig.length){pos=_get_obj_pos(orig);from={width:pos.width+(currentOpts.padding*2),height:pos.height+(currentOpts.padding*2),top:pos.top-currentOpts.padding-20,left:pos.left-currentOpts.padding-20};}else{view=_get_viewport();from={width:currentOpts.padding*2,height:currentOpts.padding*2,top:parseInt(view[3]+view[1]*0.5,10),left:parseInt(view[2]+view[0]*0.5,10)};}
return from;},_animate_loading=function(){if(!loading.is(':visible')){clearInterval(loadingTimer);return;}
$('div',loading).css('top',(loadingFrame*-40)+'px');loadingFrame=(loadingFrame+1)%12;};$.fn.fancybox=function(options){if(!$(this).length){return this;}
$(this).data('fancybox',$.extend({},options,($.metadata?$(this).metadata():{}))).unbind('click.fb').bind('click.fb',function(e){e.preventDefault();if(busy){return;}
busy=true;$(this).blur();selectedArray=[];selectedIndex=0;var rel=$(this).attr('rel')||'';if(!rel||rel==''||rel==='nofollow'){selectedArray.push(this);}else{selectedArray=$("a[rel="+rel+"], area[rel="+rel+"]");selectedIndex=selectedArray.index(this);}
_start();return;});return this;};$.fancybox=function(obj){var opts;if(busy){return;}
busy=true;opts=typeof arguments[1]!=='undefined'?arguments[1]:{};selectedArray=[];selectedIndex=parseInt(opts.index,10)||0;if($.isArray(obj)){for(var i=0,j=obj.length;i<j;i++){if(typeof obj[i]=='object'){$(obj[i]).data('fancybox',$.extend({},opts,obj[i]));}else{obj[i]=$({}).data('fancybox',$.extend({content:obj[i]},opts));}}
selectedArray=jQuery.merge(selectedArray,obj);}else{if(typeof obj=='object'){$(obj).data('fancybox',$.extend({},opts,obj));}else{obj=$({}).data('fancybox',$.extend({content:obj},opts));}
selectedArray.push(obj);}
if(selectedIndex>selectedArray.length||selectedIndex<0){selectedIndex=0;}
_start();};$.fancybox.showActivity=function(){clearInterval(loadingTimer);loading.show();};$.fancybox.hideActivity=function(){loading.hide();};$.fancybox.next=function(){return $.fancybox.pos(currentIndex+1);};$.fancybox.prev=function(){return $.fancybox.pos(currentIndex-1);};$.fancybox.pos=function(pos){if(busy){return;}
pos=parseInt(pos);selectedArray=currentArray;if(pos>-1&&pos<currentArray.length){selectedIndex=pos;_start();}else if(currentOpts.cyclic&&currentArray.length>1){selectedIndex=pos>=currentArray.length?0:currentArray.length-1;_start();}
return;};$.fancybox.cancel=function(){if(busy){return;}
busy=true;$.event.trigger('fancybox-cancel');_abort();selectedOpts.onCancel(selectedArray,selectedIndex,selectedOpts);busy=false;};$.fancybox.close=function(){if(busy||wrap.is(':hidden')){return;}
busy=true;if(currentOpts&&false===currentOpts.onCleanup(currentArray,currentIndex,currentOpts)){busy=false;return;}
_abort();$(close.add(nav_left).add(nav_right)).hide();$(content.add(overlay)).unbind();$(window).unbind("resize.fb scroll.fb");$(document).unbind('keydown.fb');content.find('iframe').attr('src',isIE6&&/^https/i.test(window.location.href||'')?'javascript:void(false)':'about:blank');if(currentOpts.titlePosition!=='inside'){title.empty();}
wrap.stop();function _cleanup(){overlay.fadeOut('fast');title.empty().hide();wrap.hide();$.event.trigger('fancybox-cleanup');content.empty();currentOpts.onClosed(currentArray,currentIndex,currentOpts);currentArray=selectedOpts=[];currentIndex=selectedIndex=0;currentOpts=selectedOpts={};busy=false;}
if(currentOpts.transitionOut=='elastic'){start_pos=_get_zoom_from();var pos=wrap.position();final_pos={top:pos.top,left:pos.left,width:wrap.width(),height:wrap.height()};if(currentOpts.opacity){final_pos.opacity=1;}
title.empty().hide();fx.prop=1;$(fx).animate({prop:0},{duration:currentOpts.speedOut,easing:currentOpts.easingOut,step:_draw,complete:_cleanup});}else{wrap.fadeOut(currentOpts.transitionOut=='none'?0:currentOpts.speedOut,_cleanup);}};$.fancybox.resize=function(){if(overlay.is(':visible')){overlay.css('height',$(document).height());}
$.fancybox.center(true);};$.fancybox.center=function(){var view,align;if(busy){return;}
align=arguments[0]===true?1:0;view=_get_viewport();if(!align&&(wrap.width()>view[0]||wrap.height()>view[1])){return;}
wrap.stop().animate({'top':parseInt(Math.max(view[3]-20,view[3]+((view[1]-content.height()-40)*0.5)-currentOpts.padding)),'left':parseInt(Math.max(view[2]-20,view[2]+((view[0]-content.width()-40)*0.5)-currentOpts.padding))},typeof arguments[0]=='number'?arguments[0]:200);};$.fancybox.init=function(){if($("#fancybox-wrap").length){return;}
$('body').append(tmp=$('<div id="fancybox-tmp"></div>'),loading=$('<div id="fancybox-loading"></div>'),overlay=$('<div id="fancybox-overlay"></div>'),wrap=$('<div id="fancybox-wrap"></div>'));outer=$('<div id="fancybox-outer"></div>').appendTo(wrap);outer.append(content=$('<div id="fancybox-content"></div>'),close=$('<a id="fancybox-close"></a>'),title=$('<div id="fancybox-title"></div>'),nav_left=$('<a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a>'),nav_right=$('<a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a>'));close.click($.fancybox.close);loading.click($.fancybox.cancel);overlay.click($.fancybox.cancel);nav_left.click(function(e){e.preventDefault();$.fancybox.prev();});nav_right.click(function(e){e.preventDefault();$.fancybox.next();});if($.fn.mousewheel){wrap.bind('mousewheel.fb',function(e,delta){if(busy){e.preventDefault();}else if($(e.target).get(0).clientHeight==0||$(e.target).get(0).scrollHeight===$(e.target).get(0).clientHeight){e.preventDefault();$.fancybox[delta>0?'prev':'next']();}});}
if(!$.support.opacity){wrap.addClass('fancybox-ie');}
if(isIE6){loading.addClass('fancybox-ie6');wrap.addClass('fancybox-ie6');$('<iframe id="fancybox-hide-sel-frame" src="'+(/^https/i.test(window.location.href||'')?'javascript:void(false)':'about:blank')+'" scrolling="no" border="0" frameborder="0" tabindex="-1"></iframe>').prependTo(outer);}};$.fn.fancybox.defaults={padding:10,margin:40,opacity:false,modal:false,cyclic:false,scrolling:'no',width:560,height:340,autoScale:true,autoDimensions:true,centerOnScroll:false,ajax:{},swf:{wmode:'transparent'},hideOnOverlayClick:true,hideOnContentClick:false,overlayShow:true,overlayOpacity:0.7,overlayColor:'#000',titleShow:true,titlePosition:'inside',titleFormat:null,titleFromAlt:false,transitionIn:'fade',transitionOut:'fade',speedIn:300,speedOut:300,changeSpeed:300,changeFade:'fast',easingIn:'swing',easingOut:'swing',showCloseButton:true,showNavArrows:true,enableEscapeButton:true,enableKeyboardNav:true,onStart:function(){},onCancel:function(){},onComplete:function(){},onCleanup:function(){},onClosed:function(){},onError:function(){}};$(document).ready(function(){$.fancybox.init();});})(jQuery);
;/*!
 * jQuery Smooth Scroll Plugin v1.4.5
 *
 * Date: Sun Mar 11 18:17:42 2012 EDT
 * Requires: jQuery v1.3+
 *
 * Copyright 2012, Karl Swedberg
 * Dual licensed under the MIT and GPL licenses (just like jQuery):
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 *
 *
 *
*/
(function(b){function m(c){return c.replace(/(:|\.)/g,"\\$1")}var n=function(c){var e=[],a=false,d=c.dir&&c.dir=="left"?"scrollLeft":"scrollTop";this.each(function(){if(!(this==document||this==window)){var g=b(this);if(g[d]()>0)e.push(this);else{g[d](1);a=g[d]()>0;g[d](0);a&&e.push(this)}}});if(c.el==="first"&&e.length)e=[e.shift()];return e},o="ontouchend"in document;b.fn.extend({scrollable:function(c){return this.pushStack(n.call(this,{dir:c}))},firstScrollable:function(c){return this.pushStack(n.call(this,
{el:"first",dir:c}))},smoothScroll:function(c){c=c||{};var e=b.extend({},b.fn.smoothScroll.defaults,c),a=b.smoothScroll.filterPath(location.pathname);this.die("click.smoothscroll").live("click.smoothscroll",function(d){var g={},i=b(this),f=location.hostname===this.hostname||!this.hostname,h=e.scrollTarget||(b.smoothScroll.filterPath(this.pathname)||a)===a,k=m(this.hash),j=true;if(!e.scrollTarget&&(!f||!h||!k))j=false;else{f=e.exclude;h=0;for(var l=f.length;j&&h<l;)if(i.is(m(f[h++])))j=false;f=e.excludeWithin;
h=0;for(l=f.length;j&&h<l;)if(i.closest(f[h++]).length)j=false}if(j){d.preventDefault();b.extend(g,e,{scrollTarget:e.scrollTarget||k,link:this});b.smoothScroll(g)}});return this}});b.smoothScroll=function(c,e){var a,d,g,i;i=0;d="offset";var f="scrollTop",h={},k=false;g=[];if(typeof c==="number"){a=b.fn.smoothScroll.defaults;g=c}else{a=b.extend({},b.fn.smoothScroll.defaults,c||{});if(a.scrollElement){d="position";a.scrollElement.css("position")=="static"&&a.scrollElement.css("position","relative")}g=
e||b(a.scrollTarget)[d]()&&b(a.scrollTarget)[d]()[a.direction]||0}a=b.extend({link:null},a);f=a.direction=="left"?"scrollLeft":f;if(a.scrollElement){d=a.scrollElement;i=d[f]()}else{d=b("html, body").firstScrollable();k=o&&"scrollTo"in window}h[f]=g+i+a.offset;a.beforeScroll.call(d,a);if(k){g=a.direction=="left"?[h[f],0]:[0,h[f]];window.scrollTo.apply(window,g);a.afterScroll.call(a.link,a)}else{i=a.speed;if(i==="auto"){i=h[f]||d.scrollTop();i/=a.autoCoefficent}d.animate(h,{duration:i,easing:a.easing,
complete:function(){a.afterScroll.call(a.link,a)}})}};b.smoothScroll.version="1.4.4";b.smoothScroll.filterPath=function(c){return c.replace(/^\//,"").replace(/(index|default).[a-zA-Z]{3,4}$/,"").replace(/\/$/,"")};b.fn.smoothScroll.defaults={exclude:[],excludeWithin:[],offset:0,direction:"top",scrollElement:null,scrollTarget:null,beforeScroll:function(){},afterScroll:function(){},easing:"swing",speed:400,autoCoefficent:2}})(jQuery);

/*!
 * jQuery Form Plugin
 * version: 3.18 (28-SEP-2012)
 * @requires jQuery v1.5 or later
 *
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses:
 *    http://malsup.github.com/mit-license.txt
 *    http://malsup.github.com/gpl-license-v2.txt
 */
(function(e){var c={};c.fileapi=e("<input type='file'/>").get(0).files!==undefined;c.formdata=window.FormData!==undefined;e.fn.ajaxSubmit=function(h){if(!this.length){d("ajaxSubmit: skipping submit process - no element selected");return this}var g,y,j,m=this;if(typeof h=="function"){h={success:h}}g=this.attr("method");y=this.attr("action");j=(typeof y==="string")?e.trim(y):"";j=j||window.location.href||"";if(j){j=(j.match(/^([^#]+)/)||[])[1]}h=e.extend(true,{url:j,success:e.ajaxSettings.success,type:g||"GET",iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},h);var s={};this.trigger("form-pre-serialize",[this,h,s]);if(s.veto){d("ajaxSubmit: submit vetoed via form-pre-serialize trigger");return this}if(h.beforeSerialize&&h.beforeSerialize(this,h)===false){d("ajaxSubmit: submit aborted via beforeSerialize callback");return this}var l=h.traditional;if(l===undefined){l=e.ajaxSettings.traditional}var p=[];var B,C=this.formToArray(h.semantic,p);if(h.data){h.extraData=h.data;B=e.param(h.data,l)}if(h.beforeSubmit&&h.beforeSubmit(C,this,h)===false){d("ajaxSubmit: submit aborted via beforeSubmit callback");return this}this.trigger("form-submit-validate",[C,this,h,s]);if(s.veto){d("ajaxSubmit: submit vetoed via form-submit-validate trigger");return this}var w=e.param(C,l);if(B){w=(w?(w+"&"+B):B)}if(h.type.toUpperCase()=="GET"){h.url+=(h.url.indexOf("?")>=0?"&":"?")+w;h.data=null}else{h.data=w}var E=[];if(h.resetForm){E.push(function(){m.resetForm()})}if(h.clearForm){E.push(function(){m.clearForm(h.includeHidden)})}if(!h.dataType&&h.target){var i=h.success||function(){};E.push(function(q){var k=h.replaceTarget?"replaceWith":"html";e(h.target)[k](q).each(i,arguments)})}else{if(h.success){E.push(h.success)}}h.success=function(H,q,I){var G=h.context||this;for(var F=0,k=E.length;F<k;F++){E[F].apply(G,[H,q,I||m,m])}};var A=e("input:file:enabled[value]",this);var n=A.length>0;var z="multipart/form-data";var v=(m.attr("enctype")==z||m.attr("encoding")==z);var u=c.fileapi&&c.formdata;d("fileAPI :"+u);var o=(n||v)&&!u;var t;if(h.iframe!==false&&(h.iframe||o)){if(h.closeKeepAlive){e.get(h.closeKeepAlive,function(){t=D(C)})}else{t=D(C)}}else{if((n||v)&&u){t=r(C)}else{t=e.ajax(h)}}m.removeData("jqxhr").data("jqxhr",t);for(var x=0;x<p.length;x++){p[x]=null}this.trigger("form-submit-notify",[this,h]);return this;function f(H){var I=e.param(H).split("&");var q=I.length;var k={};var G,F;for(G=0;G<q;G++){F=I[G].split("=");k[decodeURIComponent(F[0])]=decodeURIComponent(F[1])}return k}function r(q){var k=new FormData();for(var F=0;F<q.length;F++){k.append(q[F].name,q[F].value)}if(h.extraData){var I=f(h.extraData);for(var J in I){if(I.hasOwnProperty(J)){k.append(J,I[J])}}}h.data=null;var H=e.extend(true,{},e.ajaxSettings,h,{contentType:false,processData:false,cache:false,type:g||"POST"});if(h.uploadProgress){H.xhr=function(){var K=jQuery.ajaxSettings.xhr();if(K.upload){K.upload.onprogress=function(O){var N=0;var L=O.loaded||O.position;var M=O.total;if(O.lengthComputable){N=Math.ceil(L/M*100)}h.uploadProgress(O,L,M,N)}}return K}}H.data=null;var G=H.beforeSend;H.beforeSend=function(L,K){K.data=k;if(G){G.call(this,L,K)}};return e.ajax(H)}function D(ad){var I=m[0],H,Z,T,ab,W,K,O,M,N,X,aa,R;var L=!!e.fn.prop;var ag=e.Deferred();if(e(":input[name=submit],:input[id=submit]",I).length){alert('Error: Form elements must not have name or id of "submit".');ag.reject();return ag}if(ad){for(Z=0;Z<p.length;Z++){H=e(p[Z]);if(L){H.prop("disabled",false)}else{H.removeAttr("disabled")}}}T=e.extend(true,{},e.ajaxSettings,h);T.context=T.context||T;W="jqFormIO"+(new Date().getTime());if(T.iframeTarget){K=e(T.iframeTarget);X=K.attr("name");if(!X){K.attr("name",W)}else{W=X}}else{K=e('<iframe name="'+W+'" src="'+T.iframeSrc+'" />');K.css({position:"absolute",top:"-1000px",left:"-1000px"})}O=K[0];M={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(ah){var ai=(ah==="timeout"?"timeout":"aborted");d("aborting upload... "+ai);this.aborted=1;if(O.contentWindow.document.execCommand){try{O.contentWindow.document.execCommand("Stop")}catch(aj){}}K.attr("src",T.iframeSrc);M.error=ai;if(T.error){T.error.call(T.context,M,ai,ah)}if(ab){e.event.trigger("ajaxError",[M,T,ai])}if(T.complete){T.complete.call(T.context,M,ai)}}};ab=T.global;if(ab&&0===e.active++){e.event.trigger("ajaxStart")}if(ab){e.event.trigger("ajaxSend",[M,T])}if(T.beforeSend&&T.beforeSend.call(T.context,M,T)===false){if(T.global){e.active--}ag.reject();return ag}if(M.aborted){ag.reject();return ag}N=I.clk;if(N){X=N.name;if(X&&!N.disabled){T.extraData=T.extraData||{};T.extraData[X]=N.value;if(N.type=="image"){T.extraData[X+".x"]=I.clk_x;T.extraData[X+".y"]=I.clk_y}}}var S=1;var P=2;function Q(ai){var ah=ai.contentWindow?ai.contentWindow.document:ai.contentDocument?ai.contentDocument:ai.document;return ah}var G=e("meta[name=csrf-token]").attr("content");var F=e("meta[name=csrf-param]").attr("content");if(F&&G){T.extraData=T.extraData||{};T.extraData[F]=G}function Y(){var aj=m.attr("target"),ah=m.attr("action");I.setAttribute("target",W);if(!g){I.setAttribute("method","POST")}if(ah!=T.url){I.setAttribute("action",T.url)}if(!T.skipEncodingOverride&&(!g||/post/i.test(g))){m.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"})}if(T.timeout){R=setTimeout(function(){aa=true;V(S)},T.timeout)}function ak(){try{var am=Q(O).readyState;d("state = "+am);if(am&&am.toLowerCase()=="uninitialized"){setTimeout(ak,50)}}catch(an){d("Server abort: ",an," (",an.name,")");V(P);if(R){clearTimeout(R)}R=undefined}}var ai=[];try{if(T.extraData){for(var al in T.extraData){if(T.extraData.hasOwnProperty(al)){if(e.isPlainObject(T.extraData[al])&&T.extraData[al].hasOwnProperty("name")&&T.extraData[al].hasOwnProperty("value")){ai.push(e('<input type="hidden" name="'+T.extraData[al].name+'">').attr("value",T.extraData[al].value).appendTo(I)[0])}else{ai.push(e('<input type="hidden" name="'+al+'">').attr("value",T.extraData[al]).appendTo(I)[0])}}}}if(!T.iframeTarget){K.appendTo("body");if(O.attachEvent){O.attachEvent("onload",V)}else{O.addEventListener("load",V,false)}}setTimeout(ak,15);I.submit()}finally{I.setAttribute("action",ah);if(aj){I.setAttribute("target",aj)}else{m.removeAttr("target")}e(ai).remove()}}if(T.forceSync){Y()}else{setTimeout(Y,10)}var ae,af,ac=50,J;function V(am){if(M.aborted||J){return}try{af=Q(O)}catch(ap){d("cannot access response document: ",ap);am=P}if(am===S&&M){M.abort("timeout");ag.reject(M,"timeout");return}else{if(am==P&&M){M.abort("server abort");ag.reject(M,"error","server abort");return}}if(!af||af.location.href==T.iframeSrc){if(!aa){return}}if(O.detachEvent){O.detachEvent("onload",V)}else{O.removeEventListener("load",V,false)}var ak="success",ao;try{if(aa){throw"timeout"}var aj=T.dataType=="xml"||af.XMLDocument||e.isXMLDoc(af);d("isXml="+aj);if(!aj&&window.opera&&(af.body===null||!af.body.innerHTML)){if(--ac){d("requeing onLoad callback, DOM not available");setTimeout(V,250);return}}var aq=af.body?af.body:af.documentElement;M.responseText=aq?aq.innerHTML:null;M.responseXML=af.XMLDocument?af.XMLDocument:af;if(aj){T.dataType="xml"}M.getResponseHeader=function(au){var at={"content-type":T.dataType};return at[au]};if(aq){M.status=Number(aq.getAttribute("status"))||M.status;M.statusText=aq.getAttribute("statusText")||M.statusText}var ah=(T.dataType||"").toLowerCase();var an=/(json|script|text)/.test(ah);if(an||T.textarea){var al=af.getElementsByTagName("textarea")[0];if(al){M.responseText=al.value;M.status=Number(al.getAttribute("status"))||M.status;M.statusText=al.getAttribute("statusText")||M.statusText}else{if(an){var ai=af.getElementsByTagName("pre")[0];var ar=af.getElementsByTagName("body")[0];if(ai){M.responseText=ai.textContent?ai.textContent:ai.innerText}else{if(ar){M.responseText=ar.textContent?ar.textContent:ar.innerText}}}}}else{if(ah=="xml"&&!M.responseXML&&M.responseText){M.responseXML=U(M.responseText)}}try{ae=k(M,ah,T)}catch(am){ak="parsererror";M.error=ao=(am||ak)}}catch(am){d("error caught: ",am);ak="error";M.error=ao=(am||ak)}if(M.aborted){d("upload aborted");ak=null}if(M.status){ak=(M.status>=200&&M.status<300||M.status===304)?"success":"error"}if(ak==="success"){if(T.success){T.success.call(T.context,ae,"success",M)}ag.resolve(M.responseText,"success",M);if(ab){e.event.trigger("ajaxSuccess",[M,T])}}else{if(ak){if(ao===undefined){ao=M.statusText}if(T.error){T.error.call(T.context,M,ak,ao)}ag.reject(M,"error",ao);if(ab){e.event.trigger("ajaxError",[M,T,ao])}}}if(ab){e.event.trigger("ajaxComplete",[M,T])}if(ab&&!--e.active){e.event.trigger("ajaxStop")}if(T.complete){T.complete.call(T.context,M,ak)}J=true;if(T.timeout){clearTimeout(R)}setTimeout(function(){if(!T.iframeTarget){K.remove()}M.responseXML=null},100)}var U=e.parseXML||function(ah,ai){if(window.ActiveXObject){ai=new ActiveXObject("Microsoft.XMLDOM");ai.async="false";ai.loadXML(ah)}else{ai=(new DOMParser()).parseFromString(ah,"text/xml")}return(ai&&ai.documentElement&&ai.documentElement.nodeName!="parsererror")?ai:null};var q=e.parseJSON||function(ah){return window["eval"]("("+ah+")")};var k=function(am,ak,aj){var ai=am.getResponseHeader("content-type")||"",ah=ak==="xml"||!ak&&ai.indexOf("xml")>=0,al=ah?am.responseXML:am.responseText;if(ah&&al.documentElement.nodeName==="parsererror"){if(e.error){e.error("parsererror")}}if(aj&&aj.dataFilter){al=aj.dataFilter(al,ak)}if(typeof al==="string"){if(ak==="json"||!ak&&ai.indexOf("json")>=0){al=q(al)}else{if(ak==="script"||!ak&&ai.indexOf("javascript")>=0){e.globalEval(al)}}}return al};return ag}};e.fn.ajaxForm=function(f){f=f||{};f.delegation=f.delegation&&e.isFunction(e.fn.on);if(!f.delegation&&this.length===0){var g={s:this.selector,c:this.context};if(!e.isReady&&g.s){d("DOM not ready, queuing ajaxForm");e(function(){e(g.s,g.c).ajaxForm(f)});return this}d("terminating; zero elements found by selector"+(e.isReady?"":" (DOM not ready)"));return this}if(f.delegation){e(document).off("submit.form-plugin",this.selector,b).off("click.form-plugin",this.selector,a).on("submit.form-plugin",this.selector,f,b).on("click.form-plugin",this.selector,f,a);return this}return this.ajaxFormUnbind().bind("submit.form-plugin",f,b).bind("click.form-plugin",f,a)};function b(g){var f=g.data;if(!g.isDefaultPrevented()){g.preventDefault();e(this).ajaxSubmit(f)}}function a(j){var i=j.target;var g=e(i);if(!(g.is(":submit,input:image"))){var f=g.closest(":submit");if(f.length===0){return}i=f[0]}var h=this;h.clk=i;if(i.type=="image"){if(j.offsetX!==undefined){h.clk_x=j.offsetX;h.clk_y=j.offsetY}else{if(typeof e.fn.offset=="function"){var k=g.offset();h.clk_x=j.pageX-k.left;h.clk_y=j.pageY-k.top}else{h.clk_x=j.pageX-i.offsetLeft;h.clk_y=j.pageY-i.offsetTop}}}setTimeout(function(){h.clk=h.clk_x=h.clk_y=null},100)}e.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")};e.fn.formToArray=function(w,f){var u=[];if(this.length===0){return u}var k=this[0];var o=w?k.getElementsByTagName("*"):k.elements;if(!o){return u}var q,p,m,x,l,s,h;for(q=0,s=o.length;q<s;q++){l=o[q];m=l.name;if(!m){continue}if(w&&k.clk&&l.type=="image"){if(!l.disabled&&k.clk==l){u.push({name:m,value:e(l).val(),type:l.type});u.push({name:m+".x",value:k.clk_x},{name:m+".y",value:k.clk_y})}continue}x=e.fieldValue(l,true);if(x&&x.constructor==Array){if(f){f.push(l)}for(p=0,h=x.length;p<h;p++){u.push({name:m,value:x[p]})}}else{if(c.fileapi&&l.type=="file"&&!l.disabled){if(f){f.push(l)}var g=l.files;if(g.length){for(p=0;p<g.length;p++){u.push({name:m,value:g[p],type:l.type})}}else{u.push({name:m,value:"",type:l.type})}}else{if(x!==null&&typeof x!="undefined"){if(f){f.push(l)}u.push({name:m,value:x,type:l.type,required:l.required})}}}}if(!w&&k.clk){var r=e(k.clk),t=r[0];m=t.name;if(m&&!t.disabled&&t.type=="image"){u.push({name:m,value:r.val()});u.push({name:m+".x",value:k.clk_x},{name:m+".y",value:k.clk_y})}}return u};e.fn.formSerialize=function(f){return e.param(this.formToArray(f))};e.fn.fieldSerialize=function(g){var f=[];this.each(function(){var l=this.name;if(!l){return}var j=e.fieldValue(this,g);if(j&&j.constructor==Array){for(var k=0,h=j.length;k<h;k++){f.push({name:l,value:j[k]})}}else{if(j!==null&&typeof j!="undefined"){f.push({name:this.name,value:j})}}});return e.param(f)};e.fn.fieldValue=function(l){for(var k=[],h=0,f=this.length;h<f;h++){var j=this[h];var g=e.fieldValue(j,l);if(g===null||typeof g=="undefined"||(g.constructor==Array&&!g.length)){continue}if(g.constructor==Array){e.merge(k,g)}else{k.push(g)}}return k};e.fieldValue=function(f,m){var h=f.name,s=f.type,u=f.tagName.toLowerCase();if(m===undefined){m=true}if(m&&(!h||f.disabled||s=="reset"||s=="button"||(s=="checkbox"||s=="radio")&&!f.checked||(s=="submit"||s=="image")&&f.form&&f.form.clk!=f||u=="select"&&f.selectedIndex==-1)){return null}if(u=="select"){var o=f.selectedIndex;if(o<0){return null}var q=[],g=f.options;var k=(s=="select-one");var p=(k?o+1:g.length);for(var j=(k?o:0);j<p;j++){var l=g[j];if(l.selected){var r=l.value;if(!r){r=(l.attributes&&l.attributes.value&&!(l.attributes.value.specified))?l.text:l.value}if(k){return r}q.push(r)}}return q}return e(f).val()};e.fn.clearForm=function(f){return this.each(function(){e("input,select,textarea",this).clearFields(f)})};e.fn.clearFields=e.fn.clearInputs=function(f){var g=/^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;return this.each(function(){var i=this.type,h=this.tagName.toLowerCase();if(g.test(i)||h=="textarea"){this.value=""}else{if(i=="checkbox"||i=="radio"){this.checked=false}else{if(h=="select"){this.selectedIndex=-1}else{if(f){if((f===true&&/hidden/.test(i))||(typeof f=="string"&&e(this).is(f))){this.value=""}}}}}})};e.fn.resetForm=function(){return this.each(function(){if(typeof this.reset=="function"||(typeof this.reset=="object"&&!this.reset.nodeType)){this.reset()}})};e.fn.enable=function(f){if(f===undefined){f=true}return this.each(function(){this.disabled=!f})};e.fn.selected=function(f){if(f===undefined){f=true}return this.each(function(){var g=this.type;if(g=="checkbox"||g=="radio"){this.checked=f}else{if(this.tagName.toLowerCase()=="option"){var h=e(this).parent("select");if(f&&h[0]&&h[0].type=="select-one"){h.find("option").selected(false)}this.selected=f}}})};e.fn.ajaxSubmit.debug=false;function d(){if(!e.fn.ajaxSubmit.debug){return}var f="[jquery.form] "+Array.prototype.join.call(arguments,"");if(window.console&&window.console.log){window.console.log(f)}else{if(window.opera&&window.opera.postError){window.opera.postError(f)}}}})(jQuery);
;(function($){$(function(){try{if(typeof _wpcf7=='undefined'||_wpcf7===null)
_wpcf7={};_wpcf7=$.extend({cached:0},_wpcf7);if($('#feedback').length){_wpcf7.cached=0;}
$('div.wpcf7 > form').ajaxForm({beforeSubmit:function(formData,jqForm,options){jqForm.wpcf7ClearResponseOutput();$formCont=$('.form_container:visible');$formCont=($formCont.attr('id')=='support_form')?$formCont.parent():$formCont;$formCont.append('<div id="support_form_overlay">&nbsp;</div>');return true;},beforeSerialize:function(jqForm,options){jqForm.find('.wpcf7-use-title-as-watermark.watermark').each(function(i,n){$(n).val('');});return true;},data:{'_wpcf7_is_ajax_call':1},dataType:'json',success:function(data){$formCont=$('.form_container:visible');var ro=$(data.into).find('div.wpcf7-response-output');$(data.into).wpcf7ClearResponseOutput();$(data.into).find('.wpcf7-form-control').removeClass('wpcf7-not-valid');$(data.into).find('form.wpcf7-form').removeClass('invalid spam sent failed');if(data.captcha)
$(data.into).wpcf7RefillCaptcha(data.captcha);if(data.quiz)
$(data.into).wpcf7RefillQuiz(data.quiz);if(data.invalids){$.each(data.invalids,function(i,n){$(data.into).find(n.into).wpcf7NotValidTip(n.message);$(data.into).find(n.into).find('.wpcf7-form-control').addClass('wpcf7-not-valid');});ro.addClass('wpcf7-validation-errors');$(data.into).find('form.wpcf7-form').addClass('invalid');$(data.into).trigger('invalid.wpcf7');}else if(1==data.spam){ro.addClass('wpcf7-spam-blocked');$(data.into).find('form.wpcf7-form').addClass('spam');$(data.into).trigger('spam.wpcf7');}else if(1==data.mailSent){ro.addClass('wpcf7-mail-sent-ok');$(data.into).find('form.wpcf7-form').addClass('sent');if(data.onSentOk)
$.each(data.onSentOk,function(i,n){eval(n)});$(data.into).trigger('mailsent.wpcf7');}else{ro.addClass('wpcf7-mail-sent-ng');$(data.into).find('form.wpcf7-form').addClass('failed');$(data.into).trigger('mailfailed.wpcf7');}
if(data.onSubmit)
$.each(data.onSubmit,function(i,n){eval(n)});$(data.into).trigger('submit.wpcf7');if(1==data.mailSent)
$(data.into).find('form').resetForm().clearForm();$(data.into).find('.wpcf7-use-title-as-watermark.watermark').each(function(i,n){$(n).val($(n).attr('title'));});$('#support_form_overlay').remove();if(1==data.mailSent){if($formCont.attr('id')=='support_form'){$formCont.after('<div id="ticket_sent"><h1>'+data.message+'</h1><a id="support_sent_close" class="btn btn_gray" href="#close">Close</a></div>');$formCont.css('visibility','hidden');}else{$formCont.append('<div class="form_sent"><h1>'+data.message+'</h1><p>We appreciate your feedback.</p><a id="feedback_close" href="#">Close</a></div>');$formCont.children('div.form').css('display','none');}
$('#support_sent_close').click(function(){$.fancybox.close();});}else{$(data.into).wpcf7FillResponseOutput(data.message);}}});$('div.wpcf7 > form').each(function(i,n){if(_wpcf7.cached)
$(n).wpcf7OnloadRefill();$(n).wpcf7ToggleSubmit();$(n).find('.wpcf7-submit').wpcf7AjaxLoader();$(n).find('.wpcf7-acceptance').click(function(){$(n).wpcf7ToggleSubmit();});$(n).find('.wpcf7-exclusive-checkbox').each(function(i,n){$(n).find('input:checkbox').click(function(){$(n).find('input:checkbox').not(this).removeAttr('checked');});});$(n).find('.wpcf7-use-title-as-watermark').each(function(i,n){var input=$(n);input.val(input.attr('title'));input.addClass('watermark');input.focus(function(){if($(this).hasClass('watermark'))
$(this).val('').removeClass('watermark');});input.blur(function(){if(''==$(this).val())
$(this).val($(this).attr('title')).addClass('watermark');});});});}catch(e){}});$.fn.wpcf7AjaxLoader=function(){return this.each(function(){var loader=$('<img class="ajax-loader" />').attr({src:_wpcf7.loaderUrl,alt:_wpcf7.sending}).css('visibility','hidden');$(this).after(loader);});};$.fn.wpcf7ToggleSubmit=function(){return this.each(function(){var form=$(this);if(this.tagName.toLowerCase()!='form')
form=$(this).find('form').first();if(form.hasClass('wpcf7-acceptance-as-validation'))
return;var submit=form.find('input:submit');if(!submit.length)return;var acceptances=form.find('input:checkbox.wpcf7-acceptance');if(!acceptances.length)return;submit.removeAttr('disabled');acceptances.each(function(i,n){n=$(n);if(n.hasClass('wpcf7-invert')&&n.is(':checked')||!n.hasClass('wpcf7-invert')&&!n.is(':checked'))
submit.attr('disabled','disabled');});});};$.fn.wpcf7NotValidTip=function(message){return this.each(function(){var into=$(this);into.children('.wpcf7-form-control').css('borderColor','#D1156A');});};$.fn.wpcf7OnloadRefill=function(){return this.each(function(){var url=$(this).attr('action');if(0<url.indexOf('#'))
url=url.substr(0,url.indexOf('#'));var id=$(this).find('input[name="_wpcf7"]').val();var unitTag=$(this).find('input[name="_wpcf7_unit_tag"]').val();$.getJSON(url,{_wpcf7_is_ajax_call:1,_wpcf7:id},function(data){if(data&&data.captcha)
$('#'+unitTag).wpcf7RefillCaptcha(data.captcha);if(data&&data.quiz)
$('#'+unitTag).wpcf7RefillQuiz(data.quiz);});});};$.fn.wpcf7RefillCaptcha=function(captcha){return this.each(function(){var form=$(this);$.each(captcha,function(i,n){form.find(':input[name="'+i+'"]').clearFields();form.find('img.wpcf7-captcha-'+i).attr('src',n);var match=/([0-9]+)\.(png|gif|jpeg)$/.exec(n);form.find('input:hidden[name="_wpcf7_captcha_challenge_'+i+'"]').attr('value',match[1]);});});};$.fn.wpcf7RefillQuiz=function(quiz){return this.each(function(){var form=$(this);$.each(quiz,function(i,n){form.find(':input[name="'+i+'"]').clearFields();form.find(':input[name="'+i+'"]').siblings('span.wpcf7-quiz-label').text(n[0]);form.find('input:hidden[name="_wpcf7_quiz_answer_'+i+'"]').attr('value',n[1]);});});};$.fn.wpcf7ClearResponseOutput=function(){return this.each(function(){$(this).find('div.wpcf7-response-output').hide().empty().removeClass('wpcf7-mail-sent-ok wpcf7-mail-sent-ng wpcf7-validation-errors wpcf7-spam-blocked');$(this).find('.wpcf7-form-control').css('borderColor','#DDDDDD');});};$.fn.wpcf7FillResponseOutput=function(message){return this.each(function(){$(this).find('div.wpcf7-response-output').append(message).slideDown('fast');});};})(jQuery);
;/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('8.2=9(a,b,c){3(n.o>1&&f(b)!=="[p q]"){c=8.r({},c);3(b===g||b===u){c.0=-1}3(v c.0===\'w\'){h d=c.0,t=c.0=i x();t.y(t.z()+d)}b=f(b);4(j.2=[5(a),\'=\',c.k?b:5(b),c.0?\'; 0=\'+c.0.A():\'\',c.6?\'; 6=\'+c.6:\'\',c.7?\'; 7=\'+c.7:\'\',c.l?\'; l\':\'\'].B(\'\'))}c=b||{};h e,m=c.k?9(s){4 s}:C;4(e=i D(\'(?:^|; )\'+5(a)+\'=([^;]*)\').E(j.2))?m(e[1]):g};',41,41,'expires||cookie|if|return|encodeURIComponent|path|domain|jQuery|function||||||String|null|var|new|document|raw|secure|decode|arguments|length|object|Object|extend|||undefined|typeof|number|Date|setDate|getDate|toUTCString|join|decodeURIComponent|RegExp|exec'.split('|'),0,{})) 
;/*! Copyright (c) 2011 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.6
 * 
 * Requires: 1.2.2+
 */
(function(a){function d(b){var c=b||window.event,d=[].slice.call(arguments,1),e=0,f=!0,g=0,h=0;return b=a.event.fix(c),b.type="mousewheel",c.wheelDelta&&(e=c.wheelDelta/120),c.detail&&(e=-c.detail/3),h=e,c.axis!==undefined&&c.axis===c.HORIZONTAL_AXIS&&(h=0,g=-1*e),c.wheelDeltaY!==undefined&&(h=c.wheelDeltaY/120),c.wheelDeltaX!==undefined&&(g=-1*c.wheelDeltaX/120),d.unshift(b,e,g,h),(a.event.dispatch||a.event.handle).apply(this,d)}var b=["DOMMouseScroll","mousewheel"];if(a.event.fixHooks)for(var c=b.length;c;)a.event.fixHooks[b[--c]]=a.event.mouseHooks;a.event.special.mousewheel={setup:function(){if(this.addEventListener)for(var a=b.length;a;)this.addEventListener(b[--a],d,!1);else this.onmousewheel=d},teardown:function(){if(this.removeEventListener)for(var a=b.length;a;)this.removeEventListener(b[--a],d,!1);else this.onmousewheel=null}},a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})})(jQuery)

;jQuery.easing['jswing']=jQuery.easing['swing'];jQuery.extend(jQuery.easing,{def:'easeOutQuad',swing:function(x,t,b,c,d){return jQuery.easing[jQuery.easing.def](x,t,b,c,d);},easeInQuad:function(x,t,b,c,d){return c*(t/=d)*t+b;},easeOutQuad:function(x,t,b,c,d){return-c*(t/=d)*(t-2)+b;},easeInOutQuad:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t+b;return-c/2*((--t)*(t-2)-1)+b;},easeInCubic:function(x,t,b,c,d){return c*(t/=d)*t*t+b;},easeOutCubic:function(x,t,b,c,d){return c*((t=t/d-1)*t*t+1)+b;},easeInOutCubic:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t+b;return c/2*((t-=2)*t*t+2)+b;},easeInQuart:function(x,t,b,c,d){return c*(t/=d)*t*t*t+b;},easeOutQuart:function(x,t,b,c,d){return-c*((t=t/d-1)*t*t*t-1)+b;},easeInOutQuart:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t+b;return-c/2*((t-=2)*t*t*t-2)+b;},easeInQuint:function(x,t,b,c,d){return c*(t/=d)*t*t*t*t+b;},easeOutQuint:function(x,t,b,c,d){return c*((t=t/d-1)*t*t*t*t+1)+b;},easeInOutQuint:function(x,t,b,c,d){if((t/=d/2)<1)return c/2*t*t*t*t*t+b;return c/2*((t-=2)*t*t*t*t+2)+b;},easeInSine:function(x,t,b,c,d){return-c*Math.cos(t/d*(Math.PI/2))+c+b;},easeOutSine:function(x,t,b,c,d){return c*Math.sin(t/d*(Math.PI/2))+b;},easeInOutSine:function(x,t,b,c,d){return-c/2*(Math.cos(Math.PI*t/d)-1)+b;},easeInExpo:function(x,t,b,c,d){return(t==0)?b:c*Math.pow(2,10*(t/d-1))+b;},easeOutExpo:function(x,t,b,c,d){return(t==d)?b+c:c*(-Math.pow(2,-10*t/d)+1)+b;},easeInOutExpo:function(x,t,b,c,d){if(t==0)return b;if(t==d)return b+c;if((t/=d/2)<1)return c/2*Math.pow(2,10*(t-1))+b;return c/2*(-Math.pow(2,-10*--t)+2)+b;},easeInCirc:function(x,t,b,c,d){return-c*(Math.sqrt(1-(t/=d)*t)-1)+b;},easeOutCirc:function(x,t,b,c,d){return c*Math.sqrt(1-(t=t/d-1)*t)+b;},easeInOutCirc:function(x,t,b,c,d){if((t/=d/2)<1)return-c/2*(Math.sqrt(1-t*t)-1)+b;return c/2*(Math.sqrt(1-(t-=2)*t)+1)+b;},easeInElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);return-(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;},easeOutElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d)==1)return b+c;if(!p)p=d*.3;if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);return a*Math.pow(2,-10*t)*Math.sin((t*d-s)*(2*Math.PI)/p)+c+b;},easeInOutElastic:function(x,t,b,c,d){var s=1.70158;var p=0;var a=c;if(t==0)return b;if((t/=d/2)==2)return b+c;if(!p)p=d*(.3*1.5);if(a<Math.abs(c)){a=c;var s=p/4;}
else var s=p/(2*Math.PI)*Math.asin(c/a);if(t<1)return-.5*(a*Math.pow(2,10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p))+b;return a*Math.pow(2,-10*(t-=1))*Math.sin((t*d-s)*(2*Math.PI)/p)*.5+c+b;},easeInBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*(t/=d)*t*((s+1)*t-s)+b;},easeOutBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;return c*((t=t/d-1)*t*((s+1)*t+s)+1)+b;},easeInOutBack:function(x,t,b,c,d,s){if(s==undefined)s=1.70158;if((t/=d/2)<1)return c/2*(t*t*(((s*=(1.525))+1)*t-s))+b;return c/2*((t-=2)*t*(((s*=(1.525))+1)*t+s)+2)+b;},easeInBounce:function(x,t,b,c,d){return c-jQuery.easing.easeOutBounce(x,d-t,0,c,d)+b;},easeOutBounce:function(x,t,b,c,d){if((t/=d)<(1/2.75)){return c*(7.5625*t*t)+b;}else if(t<(2/2.75)){return c*(7.5625*(t-=(1.5/2.75))*t+.75)+b;}else if(t<(2.5/2.75)){return c*(7.5625*(t-=(2.25/2.75))*t+.9375)+b;}else{return c*(7.5625*(t-=(2.625/2.75))*t+.984375)+b;}},easeInOutBounce:function(x,t,b,c,d){if(t<d/2)return jQuery.easing.easeInBounce(x,t*2,0,c,d)*.5+b;return jQuery.easing.easeOutBounce(x,t*2-d,0,c,d)*.5+c*.5+b;}});
;/*
 * SlideTabs jQuery Plugin - www.slidetabs.com
 *
 * @version 1.0.5
 *
 * Copyright 2013, WebStack
 *
 * You need to purchase a license if you want to use this script:
 * http://www.slidetabs.com/pricing
 *
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(9($){6(!$.3g){$.3g={}}9 2O(c,d){3.$I=c;3.$Y=c.V(\'.\'+d.4g).1z();3.$1S=3.$Y.u(\'T\');3.$q=3.$1S.u(\'5y\').y(d.3h);3.$N=3.$q.u(\'m\');3.$a=3.$N.V(\'a\').y(d.1T);3.$1A=c.V(\'.\'+d.3i).1z();3.$l=3.$1A;3.$Z=3.$l.u(\'.\'+d.1U);3.$E=3.$Y.V(\'.\'+d.3j);3.$C=3.$Y.V(\'.\'+d.3k);3.$5z=$(5A);3.$F;3.$J=[];3.$m;3.$R;3.$D;3.$16;3.8={};3.e;3.A=0;3.7=d;6(!3.$a.z){x k}3.q={};3.l={};3.$I.y(\'1G\');3.4h=(3.$Z.V(\'.1G\').z)?n:k;3.$2P=3.$I.1e(\'.\'+d.1U);3.3l=3.$2P.z>0?n:k;3.q.1B=3.$N.z;3.l.2l=(3.7.17==\'3m\'||3.7.17==\'3n\')?n:k;t e=3,P,4i=/^#.+/,2m,3o,11;3.$a.1b(9(i,a){P=$(a).1f(\'P\');2m=P.3p(\'#\')[0];6(2m&&(2m===3q.5B().3p(\'#\')[0]||(3o=$(\'5C\')[0])&&2m===3o.P)){P=a.18;a.P=P}6(P&&!4i.5D(P)&&P!==\'#\'){$.2n(a,\'2o.q\',P.2Q(/#.*$/,\'\'));11=e.4j(3);a.P=\'#\'+11;e.$D=e.$l.u(\'.\'+11);6(!e.$D.z){e.$D=$(\'<T></T>\').y(11+\' \'+d.1U);e.$l.2R(e.$D);e.$Z=e.$Z.5E(e.$D)}}p{11=$(a).1f(\'2n-4k\');6(11){a.P=\'#\'+11}}});3.$N.1z().y(\'3r\');3.$N.14().y(\'2p\');3.$a.1z().y(\'3s\');3.$a.14().y(\'2S\');3.$Z.1z().y(\'5F\');6(!3.$C.z){3.$C=$(\'<a P="#" 1V="\'+d.3k+\'" />\');3.$Y.4l(3.$C)}6(!3.$E.z){3.$E=$(\'<a P="#" 1V="\'+d.3j+\'" />\');3.$Y.4l(3.$E)}t f=(\'4m\'22 23);6(d.2T&&f){3.8.1o=n}t g=9(a){a=a.5G();t b=/(4n)[ \\/]([\\w.]+)/.2q(a)||/(H)[ \\/]([\\w.]+)/.2q(a)||/(5H)(?:.*2U|)[ \\/]([\\w.]+)/.2q(a)||/(5I) ([\\w.]+)/.2q(a)||a.4o(\'5J\')<0&&/(5K)(?:.*? 5L:([\\w.]+)|)/.2q(a)||[];x{1g:b[1]||\'\',2U:b[2]||\'0\'}};t h=g(5M.5N),1g={};6(h.1g){1g[h.1g]=n;1g.2U=h.2U}6(1g.4n){1g.H=n}p 6(1g.H){1g.4p=n}6(d.12){6(f||1g.4p){6(\'4q\'22 23&&\'5O\'22 4r 4q()){3.$I.y(\'5P\');3.8.12=n;6(d.2V==0){d.2V=1}6(d.1h==0){d.1h=1}}}}6(d.24==\'2r\'){3.$1S.o(\'5Q\',\'2s\');3.8.19=\'25\';3.8.1a=\'2W\';3.8.3t=\'1H\';3.8.2X=\'4s\';3.8.2Y=4;6(3.8.12){3.8.o=\'-H-1p\';3.8.K=\'1W(\';3.8.v=\'v,U,U)\'}p{3.8.o=\'4t\';3.8.K=\'\';3.8.v=\'v\'}}p{3.8.19=\'1C\';3.8.1a=\'W\';3.8.3t=\'B\';3.8.2X=\'4u\';3.8.2Y=5;6(3.8.12){3.8.o=\'-H-1p\';3.8.K=\'1W(U,\';3.8.v=\'v,U)\'}p{3.8.o=\'4v\';3.8.K=\'\';3.8.v=\'v\'}t j=3.$E.W(n),3u=3.$C.W(n);3.8.4w=(j>=3u)?j:3u}6(d.2Z.z>0){3.4x()}6(d.2t.z>0){3.4y()}3.4z();6(d.1q&&!3.26){3.4A()}$.1b($.3g,9(a,b){b.1r(e)})};2O.4B={4x:9(){6(3.7.2Z==\'27\'){3.$I.o(\'1H\',\'4C%\')}p{3.$I.o(\'1H\',3.7.2Z+\'v\')}},4y:9(){t a=(3.$1A.W(n)-3.$1A.B()),28;6(3.7.24==\'5R\'){t b=(3.$Y.W(k)-3.$Y.B());28=(3.7.2t-a);3.$Y.o(\'B\',(3.7.2t-b)+\'v\');3.$1A.o(\'B\',28+\'v\')}p{28=(3.7.2t-(3.$Y.W(n)+a));3.$1A.o(\'B\',28+\'v\')}3.l.31=28},4z:9(){t a=3.q;a.1s=\'#\'+3.$I.1f(\'29\')+\' .\'+3.7.3h+\':1s\';a.32=k;a.3v=3.$a.z;a.5S=3.$Y[3.8.1a](k);a.5T=3.$q.W(n);a.2u=3.1D();a.4D=(3.$E.2a(\':33\')||3.$C.2a(\':33\'))?n:k;3.1t();3.4E();3.4F()},4j:9(a){t b=$(a).1f(\'2n-4k\');x b&&b.2Q(/\\s/g,\'5U\').2Q(/[^\\w\\5V-\\5W-]/g,\'\')||\'F-\'+3.1B++},3w:9(a){t b=3;b.$a.1b(9(){6($(3).1f(\'P\')==\'#\'+a){b.3v++;b.11=\'F-\'+b.3v;b.3w(b.11);x}})},1D:9(){t a=3,3x=0;a.$q.u(\'m\').1b(9(){3x+=2b($(3).o(a.8.3t))});x 3x},1t:9(){6(3.7.L==0){6(3.7.24==\'2r\'){3.8.L=3.$1S.2W(k)}p{t a=3.$1S.M().1C;6(3.$I.1I(3.7.1i)){a=(a==0)?3.8.4w:a}3.8.L=(2b(3.$Y.o(\'B\'))-a)}}p{3.8.L=3.7.L}},4F:9(){t c=3,18;6(c.7.4G==n){t d=1J,5X;$(23).5Y(9(){6(d){4H(d)}d=34(9(){6(c.$I.2a(\':2s\')){x k}6(c.7.24==\'2r\'){c.4I()}p{c.4J()}6(c.7.1K==n&&!c.4h){c.2c(n)}},4C)})}c.$E.1j(9(){6(c.q.Q){x k}c[\'4K\'+c.7.1c+\'5Z\']();x k}),c.$C.1j(9(){6(c.q.Q){x k}c[\'4K\'+c.7.1c+\'60\']();x k}),c.$q.61(\'m a.\'+c.7.1T,\'1j\',9(){6(c.q.Q){x k}c.1u(3,n);6(c.7.3y==k){x k}});6($.2v.3z&&c.7.35){c.$q.3z(9(a,b){6(c.q.Q){x k}(b>0)?c.36():c.37();x k})}6(c.7.4L){$(\'.\'+c.7.3A).1b(9(){6($(3).1f(\'3B\')==c.$I.1f(\'29\')){$(3).1j(9(){6(c.q.Q){x k}18=c.2w($(3));c.$F=c.3C(18);c.1u(c.$F);6(c.7.3y==k){x k}})}})}},4I:9(){3.1t();t a=3.1D(),4M=(3.4D)?2b(3.$1S.o(\'1H\')):2b(3.$Y.o(\'1H\'));6(3.$I.1I(3.7.1i)){6(X(3.2d)==\'1X\'){3.2d=(3.q.2u-a)}p{6(3.2d<5){a=(a+3.2d)}}}6(a<=4M){3.A=(0+3.7.S);3.38();3.$q.o(3.8.o,3.8.K+ -+3.A+3.8.v)}p{t b=2b(3.$1S.o(\'1H\'))-(3.$R.M().25+3.$R.2W(k));6(b>(0+3.7.S)){3.A=(3.A-b);3.2x();3.1E(3.$C);3.13(3.$E)}p{3.2e()}3.$I.y(3.7.1i);3.2y()}3.1t();6(3.8.1o){3.2z()}},4J:9(){6(3.3D){4H(3.3D)}t b=3;3.3D=34(9(){b.1t();6(b.$q.W(k)<b.$Y.W(n)){b.A=(0+b.7.S);b.38();b.$q.o(b.8.o,b.8.K+ -+b.A+b.8.v)}p{t a,$R=b.$N.14(),$3E,1Y=b.8.L-($R.M().1C+$R.W(k)),3F=k,3G=k;6(1Y>(0+b.7.S)){b.A=(b.A-1Y);b.2x();b.1E(b.$C);b.13(b.$E)}p{b.$N.1b(9(){a=$(3);1Y=a.M().1C;6(1Y==(0+b.7.1v)){3F=n}p 6((1Y+a.u(\'a\').W(k))==(b.8.L-b.7.S)){3G=n;x k}p 6(1Y<0){$3E=a}});6(!3F&&!3G){b.A=(b.A-39.3H($3E.M().1C));b.2x()}b.2e()}b.$I.y(b.7.1i);b.2y()}b.1t();6(b.8.1o){b.2z()}},62)},4E:9(){3.4N();3.3I(n);3.$R=3.$q.u(\'m:14\');3.$F=3.$J;3.$J=3.$J.1e(\'m\');6((3.$R[3.8.1a](k)+3.$R.M()[3.8.19])>3.8.L){3.$I.y(3.7.1i);3.2y();3.1t();3.4O(3.$F[3.8.1a](k),3.$J.M()[3.8.19]);6(!3.7.1k){3.2e()}}},4O:9(a,b){3.8.1w=a;3.8.O=b;6(3.8.O>3.8.L){3.A=(3.8.1w+(3.8.O-3.8.L));3.A=(3.A+3.7.S)}p 6((3.8.O+3.8.1w)>3.8.L){3.A=(3.8.1w-(3.8.L-3.8.O));3.A=(3.A+3.7.S)}p{3.A=(3.A-3.7.1v)}3.2x()},2x:9(){6(3.8.12){3.$q.o(\'-H-1d-1F\',\'2A\')}3.$q.o(3.8.o,3.8.K+-3.A+3.8.v)},4P:9(a){t b=3.1D();6(b>3.8.L-3.7.S){3.$I.y(3.7.1i);3.2y();3.1L();3.1t();6(a==n){b=3.1D();3.A=(b-3.8.L)+3.7.S;3.1M(3J)}}},2e:9(){6(3.7.1c==\'1Z\'&&!3.7.1k){6(3.$N.1z().M()[3.8.19]==(0+3.7.1v)){3.1E(3.$E)}p{3.13(3.$E)}6((3.$R.M()[3.8.19]+3.$R[3.8.1a](k))<=(3.8.L-3.7.S)){3.1E(3.$C)}p{3.13(3.$C)}}p{3.1L()}},13:9(a){a.1x(3.7.3K)},1E:9(a){a.y(3.7.3K)},2y:9(){3.$E.o(\'4Q\',\'4R\');3.$C.o(\'4Q\',\'4R\');6(X(3.2d)==\'1X\'){t a=3.1D();3.2d=39.3H(3.q.2u-a)}},38:9(){3.$I.1x(3.7.1i);3.$E.2f();3.$C.2f()},1u:9(a,b,c,d){6(3.l.Q||3.3L){x k}3.$F=$(a);6(3.$F.1I(3.7.15)){x k}6(X(3.7.3M)==\'9\'){3.7.3M.1r(3.$F)}t e=$.2n(3.$F[0],\'2o.q\');3.$m=3.$F.1e(\'m\');3.4S();3.8.O=3.$m.M();3.8.63=3.$J.1l(\'m\').M();3.4T=(c)?n:k;3.4U();6(3.7.1q==n){6(b){6(3.7.4V){3.7.1q=k;3.20()}p{3.8.G=3.$F.1e(\'m\').G();6(!3.3a){3.2B()}}}}3.q.32=(d)?d:k;6(e){3.3N(3.$F,e,c,n)}p{3.3O(3.$F,c)}},3N:9(a,b,c,d){3.3L=n;6(3.26){3.26.64();65 3.26}6(3.7.1q==n){3.20()}6(3.7.4W==n){3.$I.2R(\'<4X 29="4Y"></4X>\')}t e=3;3.26=$.66({67:b,68:\'3P\',69:9(r){$(e.$Z[a.1l(\'m\').G()]).3P(\'<T 1V="\'+e.7.1N+\'">\'+r+\'</T>\');6(e.7.4Z){a.6a(\'2o.q\')}6(X(e.7.3Q)==\'9\'){e.7.3Q.1r(a)}},6b:9(){$(e.$Z[a.1l(\'m\').G()]).3P(\'<T 1V="\'+e.7.1N+\'">\'+e.7.50+\'</T>\')},6c:9(){6(d){e.3O(a,c)}p{6(e.7.1K==n){e.2c(k)}}e.3L=k;e.26=k;$(\'#4Y\').3R();6(e.7.1q){e.8.G=a.1e(\'m\').G();e.2B()}}})},3O:9(a,b){3.21(n,\'6d\');3.8.18=3.2w(a);3.$16=3.$l.u(\'.\'+3.7.1m).1x(3.7.1m);3.$D=3.$l.u(\'.\'+3.8.18).y(3.7.1m);3.$2g=3.$D;6(3.7.1K==n){3.2c(n)}6(3.8.12&&3.l.2l){3.51()}6(b>0&&3.l.1o){3[\'52\'+3.7.17](b)}p{6(3.7.17.z>0){3[\'52\'+3.7.17](b)}p{3.$16.o({M:\'2C\',2D:\'2s\'});3.$D.o({M:\'6e\',2D:\'33\'});3.l.Q=k}}},53:9(){6(3.q.Q||$(3.l.1s).z){x k}3.8.$3S=3.3T(\'E\');6(3.8.$3S.z){3.1u(3.8.$3S,n)}p{6(3.7.1k==n){3.1u(3.$q.u(\'m\').V(\'a\').14(),n,0,\'E\')}}},54:9(){6(3.q.Q||$(3.l.1s).z){x k}3.8.$3U=3.3T(\'C\');6(3.8.$3U.z){3.1u(3.8.$3U,n)}p{6(3.7.1k==n){3.1u(3.$q.u(\'m\').V(\'a\').1z(),n,0,\'C\')}}},3T:9(a){x 3.$F.1e(\'m\')[a]().V(\'a.\'+3.7.1T)},3C:9(a){x 3.$q.V(\'[3B=\'+a+\']\')},2w:9(a){3.8.18=a.1f(\'P\');x 3.8.18.6f((3.8.18.4o(\'#\')+1))},4N:9(){6(3.7.55==n&&3q.18){3.$J=3.3C(3q.18.6g(1))}6(!3.$J.z){t a=(3.7.56==n&&$.3b)?$.3b(3.$I.1f(\'29\')):k;6(a){3.3V();3.$J=3.$a.2h(a).y(3.7.15);6(!3.$J.z){3.3W()}}p{3.$J=3.$q.u(\'m\').V(\'.\'+3.7.15);6(!3.$J.z){3.3W()}}3.$J.1l(\'m\').y(3.7.1O)}p{3.3V();3.$J.y(3.7.15).1l(\'m\').y(3.7.1O)}3.3X(3.$J)},3W:9(){3.$J=3.$q.V(\'a:1z\').y(3.7.15)},3V:9(){3.$q.u(\'m\').V(\'.\'+3.7.15).1x(3.7.15).1l(\'m\').1x(3.7.1O)},4S:9(){3.$J=3.$q.u(\'m\').V(\'a.\'+3.7.15).1x(3.7.15);3.$J.1l(\'m\').1x(3.7.1O);3.$F.y(3.7.15).1l(\'m\').y(3.7.1O);3.3X(3.$F)},3X:9(a){6($.3b){$.3b(3.$I.1f(\'29\'),a.1e(\'m\').G())}},4U:9(){6(3.q.Q){x k}3.8.O=3.8.O[3.8.19];3.8.1w=3.$m[3.8.1a](k);3.8.3Y=3.$m.u(\'a\')[3.8.1a](k);3.8.57=(3.$m.C().z==1)?3.$m.C().M()[3.8.19]:0;6(3.8.O<(0+3.7.1v)){3.q.Q=n;3.8.2i=(3.8.1w-3.8.57);3.A=(3.A-(3.8.2i+3.7.1v));3.13(3.$C);3.1M()}p 6((3.8.3Y+3.8.O)>(3.8.L-3.7.S)){3.q.Q=n;3.A+=(3.8.3Y-(3.8.L-(3.8.O+3.7.S)));3.13(3.$E);3.1M()}3.1L()},36:9(b){6($(3.q.1s).z||!3.$I.1I(3.7.1i)){x k}3.q.Q=n;6(X(3.7.3Z)==\'9\'){3.7.3Z.1r(3.$F)}t c=3,$N=3.$q.u(\'m\');$N.1b(9(){c.$m=$(3);c.8.O=c.$m.M()[c.8.19];6(c.8.O>=(-1+c.7.1v)){6(c.7.2E>1){t a=c.$m.G(),G=((a-c.7.2E)),58=(a>0)?1:0;G=(G<0)?58:(G+1);c.$m=$N.2h(G);c.8.O=c.$m.M()[c.8.19]}c.$m=c.$m.E();6(!c.$m.z){6(c.7.1k&&X(b)==\'1X\'){c.$R=$N.14();c.8.O=c.$R.M()[c.8.19];c.A=((((c.8.O+c.$R[c.8.1a](k))-c.7.1v)-c.8.L)+c.7.S);c.$m=c.$R}p{c.q.Q=k}}p{c.8.2i=(c.$m[c.8.1a](n)-c.8.O);c.A=((c.A-c.8.2i)-c.7.1v)}6(c.$m.z){c.1M(b)}6(c.7.1c==\'1Z\'){c.1L(c.$C)}x k}})},37:9(a){6($(3.q.1s).z||!3.$I.1I(3.7.1i)){x k}3.q.Q=n;6(X(3.7.40)==\'9\'){3.7.40.1r(3.$F)}t b=3,$N=3.$q.u(\'m\'),$2F;$N.1b(9(){b.$m=$(3);$2F=b.$m.u(\'a\');b.8.1w=$2F[b.8.1a](k);b.8.O=b.$m.M()[b.8.19];6(39.6h(b.8.1w+b.8.O)>(b.8.L+39.3H(b.7.S))){6(b.7.2E>1){b.$m=$N.2h((b.$m.G()+b.7.2E)-1);6(!b.$m.z){b.$m=$N.14()}$2F=b.$m.u(\'a\');b.8.1w=$2F[b.8.1a](k);b.8.O=b.$m.M()[b.8.19]}b.8.2i=(b.8.L-b.8.O);b.A+=((b.8.1w-b.8.2i)+b.7.S);b.1M(a);6(b.7.1c==\'1Z\'){b.1L(b.$E)}x k}p 6(b.$m.G()+1==b.$a.z){6(b.7.1k==n&&X(a)==\'1X\'){b.A=(0-b.7.1v);b.1M(a);6(b.7.1c==\'1Z\'){b.1L(b.$E)}}p{b.q.Q=k}}})},1M:9(a){t b=3,3c=(a>0)?a:b.7.2V;6(b.8.12){b.59();b.$q.o({\'-H-1d-1F\':3c+\'41\',\'-H-1d-42-9\':\'2G-2H\',\'-H-1p\':b.8.K+ -b.A+b.8.v})}p{6(b.7.24==\'2r\'){b.$q.1P({\'4t\':-+b.A+\'v\'},3c,b.7.43,9(){b.3d(k,\'1Q\')})}p{b.$q.1P({\'4v\':-+b.A+\'v\'},3c,b.7.43,9(){b.3d(k,\'1Q\')})}}},1L:9(a){6(!3.7.1k){6(3.7.1c==\'1j\'){3.$m=3.$F.1e(\'m\')}6(3.$m.2a(\':1z-5a\')){3.1E(3.$E);3.13(3.$C)}p 6(3.$m.2a(\':14-5a\')){3.1E(3.$C);3.13(3.$E)}p{6(a){3.13(a)}p 6(3.7.1c==\'1j\'){3.13(3.$E);3.13(3.$C)}}}},6i:9(e){6(X e==\'1X\')e=23.6j;6(X e.5b==\'1X\')e.5b=e.6k;6(X e.5c==\'1X\')e.5c=e.6l;x e},6m:9(a,b){t c=23.6n(a.6o(0),1J).6p(\'-H-1p\'),5d=c.2Q(/^6q\\(/i,\'\').3p(/, |\\)$/g),8=2b(5d[b],10);x(6r(8))?0:8},59:9(){t a=3;a.$q.2I(\'3e\').5e(\'3e\',9(){a.3d(k,\'1Q\')})},3d:9(a,b){3.q.Q=a;6(3.7.1q){3[\'5f\'+b](k,n)}},3I:9(a){t b=3.l;6(3.7.17==\'3n\'){b.5g=\'W\';b.1y=\'B\';b.2X=\'4u\';b.2Y=5;6(3.8.12){b.o=\'-H-1p\';b.K=\'1W(U,\';b.v=\'v,U)\'}p{b.o=\'1C\';b.K=\'\';b.v=\'v\'}}p{b.5g=\'2W\';b.1y=\'1H\';b.2X=\'4s\';b.2Y=4;6(3.8.12){b.o=\'-H-1p\';b.K=\'1W(\';b.v=\'v,U,U)\'}p{b.o=\'25\';b.K=\'\';b.v=\'v\'}}b.Q=k;b.6s=0;6(a==n){b.1s=\'#\'+3.$I.1f(\'29\')+\' .\'+3.7.3i+\' :1s\';b.31=0;b.B=0;3.5h();t c=$.2n(3.$J[0],\'2o.q\');6(c){3.3N(3.$J,c)}}},5i:9(){3.l.5j=3.l.o;3.3I(k);6(3.8.12){3.$Z.o(\'-H-1d-1F\',\'\')}3.$Z.o(3.l.5j,\'\').o(\'2D\',\'\');3.44()},5h:9(){t a=3.2w(3.$J);3.$D=3.$l.u(\'.\'+a).y(3.7.1m);3.$2g=3.$D;6(3.7.1K==n){t b=3.$D.u(\'.\'+3.7.1N).o(\'B\',\'27\');6(b.z){3.l.B=b.W(n)}p{3.$Z.o(\'B\',\'27\');3.l.B=3.$D.W(n)}3.l.31=3.l.B;3.$l.o(\'B\',3.l.B+\'v\')}3.44()},44:9(){6(3.7.17){6(3.8.12){3.$Z.o(\'-H-1d-1F\',\'2A\');3.$D.o(3.l.o,3.l.K+\'0\'+3.l.v)}3.$l.u(\'T\').o(\'M\',\'2C\').5k(\'T.\'+3.7.1m).o(3.l.o,3.l.K+3.7.2J+3.l.v)}p{3.$Z.5k(\'T.\'+3.7.1m).o({M:\'2C\',2D:\'2s\'})}},2j:9(){6(3.8.12){3.$Z.o(\'-H-1d-1F\',\'2A\')}3.$16.o(3.l.o,3.l.K+3.7.2J+3.l.v).45();6(3.4T){t a=(3.$2g.G()>3.$16.G())?3.$16.E():3.$16.C();a.o(3.l.o,3.l.K+3.7.2J+3.l.v).45()}},5l:9(a){t b=3,$3,$l,$46,1B=b.$2P.z,3f,B;b.$2P.1b(9(i){$3=$(3);$l=$3.1l();3f=((i+1)==1B)?n:k;6(3f){6(!$3.1I(b.7.1m)){x k}}$46=$l.u(\'.\'+b.7.1m).u(\'.\'+b.7.1N).o(\'B\',\'27\');B=b.47($46,$3);6(3f&&b.7.2K>0&&a){$l.1P({\'B\':B+\'v\'},b.7.2K)}p{$l.o(\'B\',B+\'v\')}})},2c:9(a){3.$D.o(\'B\',\'27\');t b=3.$D.u(\'.\'+3.7.1N).o(\'B\',\'27\');3.l.B=3.47(b,3.$D);6(!3.3l&&3.7.2K>0&&a){3.$l.1P({\'B\':3.l.B+\'v\'},3.7.2K)}p{3.$l.o(\'B\',3.l.B+\'v\');6(3.3l){3.5l(a)}}},47:9(a,b){t c=a.W(n);6(c==0||c==1J){c=b.W(n);6(c==0){c=3.l.31}}x c},5m:9(){3.$1A.48(\'49\');3.$l.48(\'49\');3.$D.u(\'.\'+3.7.1N).48(\'49\')},6t:9(){t a=3;a.$D.2f().o(a.l.o,a.l.K+0+a.l.v).5n(a.7.1h,9(){a.21(k,\'1Q\');6(X(a.7.1n)==\'9\'){a.7.1n.1r(a.$F)}});a.$16.5o(a.7.1h,9(){a.2j()})},6u:9(){t a=3;a.$D.2f().o(a.l.o,a.l.K+0+a.l.v);a.$16.5o(a.7.1h,9(){a.$D.5n(a.7.1h,9(){a.2j();a.21(k,\'1Q\')});6(X(a.7.1n)==\'9\'){a.7.1n.1r(a.$F)}})},2L:9(a,b){3.$16.o({\'-H-1d-1F\':a+\'41\',\'-H-1d-42-9\':b,\'-H-1p\':3.l.K+3.8.2M+3.l.v});3.$D.o({\'-H-1d-1F\':a+\'41\',\'-H-1d-42-9\':b,\'-H-1p\':\'1W(U,U,U)\'})},51:9(a){t b=3;b.$2g.5e(\'3e\',9(){b.$2g.2I(\'3e\');6(a){b.6v()}p{b.2j()}b.21(k,\'1Q\');6(X(b.7.1n)==\'9\'){b.7.1n.1r(b.$F)}})},6w:9(a){t b=3;b.8.1y=b.$1A.1H();b.4a();6(b.8.12){6(a>0){b.2L(a,\'2G-2H\')}p{b.$D.o({\'-H-1d-1F\':\'2A\',\'-H-1p\':\'1W(\'+b.8.2k+\'v,U,U)\'});34(9(){b.2L(b.7.1h,\'2G-22-2H\')},30)}}p{6(a>0){b.8.1R=\'5p\'}p{b.$D.o(\'25\',b.8.2k);a=b.7.1h;b.8.1R=b.7.4b}b.$16.1P({\'25\':b.8.2M},a,b.8.1R);b.$D.1P({\'25\':\'U\'},a,b.8.1R,9(){b.2j();b.21(k,\'1Q\');6(X(b.7.1n)==\'9\'){b.7.1n.1r(b.$F)}})}},6x:9(a){t b=3;b.8.1y=b.$1A.B();6(b.l.B>b.8.1y){b.8.1y=b.l.B}b.4a();6(b.8.12){6(a>0){b.2L(a,\'2G-2H\')}p{b.$D.o({\'-H-1d-1F\':\'2A\',\'-H-1p\':\'1W(U,\'+b.8.2k+\'v,U)\'});34(9(){b.2L(b.7.1h,\'2G-22-2H\')},30)}}p{6(a>0){b.8.1R=\'5p\'}p{b.$D.o(\'1C\',b.8.2k);a=b.7.1h;b.8.1R=b.7.4b}b.$16.1P({\'1C\':b.8.2M},a,b.8.1R);b.$D.1P({\'1C\':\'U\'},a,b.8.1R,9(){b.2j();b.21(k,\'1Q\');6(X(b.7.1n)==\'9\'){b.7.1n.1r(b.$F)}})}},4a:9(){6(3.q.32!=k){3.l.4c=(3.q.32==\'C\')?n:k}p{3.l.4c=(3.$16.G()<3.$D.G())?n:k}6(3.l.4c){3.8.2M=-3.8.1y;3.8.2k=3.8.1y}p{3.8.2M=3.8.1y;3.8.2k=-3.8.1y}},21:9(a,b){3.l.Q=a;6(3.7.1q){3[\'5f\'+b](k,n)}},4A:9(){3.8.G=(3.$J.G()>=0)?3.$J.G():0;3.3a=k;3.2B()},2B:9(){t a=3;a.20();a.5q=6y(9(){a.5r()},a.7.5s)},20:9(){6z(3.5q)},5r:9(){6(!3.$I.2a(\':33\')){3.20();x k}3.8.G++;6(3.8.G==3.$a.z){3.8.G=0}6(3.7.1k==n){3.1u($(3.$a[3.8.G]),k,0,\'C\')}p{3.1u($(3.$a[3.8.G]))}},5t:9(a){6(a){3.7.1q=k}3.3a=n;3.20()},5u:9(a){6(a){3.7.1q=n}3.3a=k;3.2B()},6A:9(a,b,c){t d=3.q;6($(d.1s).z){x k}d.1B++;d.11=\'F-\'+d.1B;3.3w(d.11);3.$a.14().1x(\'2S\').1e(\'m\').1x(\'2p\');3.$q.2R(\'<m><a P="#\'+d.11+\'" 3B="\'+d.11+\'" 1V="\'+3.7.1T+\' 6B\'+d.1B+\'">\'+a+\'</a></m>\');3.$l.2R(\'<T 1V="\'+d.11+\' \'+3.7.1U+\'"><T 1V="\'+3.7.1N+\'">\'+b+\'</T></T>\');3.$N=3.$q.u(\'m\');3.$m=3.$N.14();3.$R=3.$m;3.$a=3.$N.V(\'a\');3.$Z=3.$l.u(\'.\'+3.7.1U);6(d.1B==1){3.$l.u(\'T\').y(3.7.1m).o(\'M\',\'2C\').o(3.l.o,3.l.K+\'0\'+3.l.v);3.$a.y(\'3s \'+3.7.15).1l(\'m\').y(\'3r \'+3.7.1O)}p{t e={};e[\'M\']=\'2C\';6(3.7.17){e[3.l.o]=3.l.K+3.7.2J+3.l.v}p{e[\'2D\']=\'2s\'}3.$l.u(\'T\').14().o(e);3.$a.14().y(\'2S\').1l(\'m\').y(\'2p\')}d.2u=3.1D();3.4P(c);6(3.8.1o){3.2z();6(3.l.2l){3.4d()}}},6C:9(a){6($(3.l.1s).z){x k}t b=3.$q.u(\'m\').z;a=a>=1?a-1:b-1;3.$m=3.$q.u(\'m\').2h(a);6(3.$m.u(\'a\').1I(3.7.15)){t c;6(a==0){c=3.$m.C().y(\'3r\');c=c.z>0?c.u(\'a\').y(\'3s\'):3.$m.u(\'a\')}p{c=3.$m.E().u(\'a\')}3.8.18=3.2w(c);c.1e(\'m\').y(3.7.1O);c.y(3.7.15);3.$D=3.$l.u(\'.\'+3.8.18).45().o(3.l.o,3.l.K+\'0\'+3.l.v).y(3.7.1m);3.$2g=3.$D;6(3.7.1K==n){3.2c(n)}3.$F=3.$m.E().u(\'a.\'+3.7.1T)}6(3.$m.1I(\'2p\')){3.$m.E().y(\'2p\').u(\'a\').y(\'2S\')}3.$m.3R();3.$l.u(\'T\').2h(a).3R();t d=3.1D();6(d>3.$Y[3.8.1a](k)-3.7.S){3.A=d-3.8.L+3.7.S;6(3.7.1c==\'1Z\'){3.13(3.$E);3.1E(3.$C)}p{6((b-2)==3.$F.1e(\'m\').G()){3.1E(3.$C)}}}p{3.A=0;3.$E.2f();3.$C.2f();3.$I.1x(3.7.1i);3.q.2u=3.1D();3.1t()}3.1M(3J);3.$N=3.$q.u(\'m\');3.$R=3.$N.14();3.$a=3.$N.V(\'a\');3.$Z=3.$l.u(\'.\'+3.7.1U);3.q.1B=3.$a.z;6(3.8.1o){3.2z()}},6D:9(a){t b=3.$a.2h(a-1);6(b.z){3.1u(b)}},6E:9(){3.53()},6F:9(){3.54()},6G:9(){3.36()},6H:9(){3.37()},6I:9(c){$.1b(c,9(a,b){6(b==\'n\'){c[a]=n}p 6(b==\'k\'){c[a]=k}});t d=(c.17!=3.7.17)?n:k;3.7=$.5v(n,{},3.7,c);3.l.2l=(c.17==\'3m\'||c.17==\'3n\')?n:k;6(c.L>0){3.1t();6(3.8.1o){3.2z()}}6(c.1c==\'1j\'){3.1L()}p 6(c.1c==\'1Z\'){3.$R=3.$q.u(\'m:14\');3.2e()}6(c.1k==n){3.13(3.$E);3.13(3.$C)}p 6(c.1k==k){3.2e()}6(3.7.35==n){t e=3;e.$q.3z(9(a,b){(b>0)?e.36():e.37();x k})}p 6(3.7.35==k){3.$q.4e()}6(c.1K==n){3.6J()}p 6(c.1K==k){3.5m()}6(d){3.5i()}6(3.8.1o){6(3.l.2l){3.4d()}p{3.4f()}}p{6(c.2T==n){6(\'4m\'22 23){3.8.1o=n;3.6K();3.4d()}}p 6(c.2T==k){3.5w();3.4f()}}},6L:9(){3.2c(n)},6M:9(){3.5t(n)},6N:9(){3.5u(n)},6O:9(){3.20();3.$q.6P(\'m a.\'+3.7.1T,\'1j\').o(3.8.o,3.8.K+\'0\'+3.8.v);3.$E.2I(\'1j\');3.$C.2I(\'1j\');3.38();6($.2v.4e){3.$q.4e()}6(3.8.1o){3.5w();3.4f()}$(\'a.\'+3.7.3A).1b(9(){$(3).2I(\'1j\')})},6Q:9(){x 3.7}};$.6R=2O.4B;$.2v.1G=9(a){t b,7=$.5v(n,{},$.2v.1G.5x,a),2N=[];3.1b(9(){b=3;6(!b.1G){b.1G=4r 2O($(b),7)}2N.6S(b.1G)});x 2N.z>1?2N:2N[0]};$.2v.1G.5x={4Z:n,50:\'6T 6U 2o l.\',4W:k,1q:k,4V:k,5s:6V,1K:k,2K:0,1c:\'1Z\',6W:\'6X\',3K:\'6Y\',3k:\'6Z\',3j:\'70\',3A:\'71\',72:\'73\',1T:\'74\',15:\'75\',1O:\'76\',1i:\'77\',4g:\'78\',3h:\'79\',1U:\'7a\',1m:\'7b\',1N:\'7c\',3i:\'7d\',7e:\'7f\',17:\'3m\',1h:7g,4b:\'7h\',4L:k,S:0,1v:0,3Q:1J,1n:1J,3M:1J,40:1J,3Z:1J,24:\'2r\',4G:k,2V:3J,43:\'\',1k:k,56:k,35:n,3y:k,L:0,2E:1,2t:\'\',2Z:\'\',2T:k,55:k,12:n,2J:\'7i\'}})(7j);',62,454,'|||this|||if|conf|val|function|||||||||||false|content|li|true|css|else|tabs|||var|children|px||return|addClass|length|margin|height|next|view|prev|tab|index|webkit|container|tabActive|pre|tabsSlideLength|position|lis|elemP|href|isAnim|liLast|offsetBR|div|0px|find|outerHeight|typeof|tabsCont|views||slug|useWebKit|tabs_enableButton|last|classTabActive|viewActive|contentAnim|hash|topleft|outerWH|each|buttonsFunction|transition|parents|attr|browser|contentAnimSpeed|classTabSlidingEnabled|click|tabsLoop|parent|classViewActive|onContentVisible|isTouch|transform|autoplay|call|animated|tabs_setSlideLength|tabs_click|offsetTL|elemD|removeClass|wh|first|contentCont|total|top|tabs_getTotalLength|tabs_disableButton|duration|slidetabs|width|hasClass|null|autoHeight|tabs_setButtonState|tabs_animate|classViewInner|classTabActiveParent|animate|resume|easing|tabsInnerCont|classTab|classView|class|translate3d|undefined|gap|slide|autoplay_clearInterval|content_setIsAnim|in|window|orientation|left|xhr|auto|newContentHeight|id|is|parseInt|content_setHeight|tabsDiff|tabs_initButtons|hide|currentView|eq|elemHidden|content_rePositionView|cssVal|animIsSlide|hrefBase|data|load|st_li_last|exec|horizontal|hidden|totalHeight|tabsOrgWidth|fn|tabs_getHash|tabs_posTabs|tabs_showButtons|tabs_setSwipeLength|0ms|autoplay_setInterval|absolute|visibility|tabsToSlide|thisA|ease|out|unbind|viewportOffset|autoHeightSpeed|content_webKitSlide|animVal|returnArr|SlideTabs|parentViews|replace|append|st_tab_last|touchSupport|version|tabsAnimSpeed|outerWidth|clientXY|arrPos|totalWidth||orgHeight|loop|visible|setTimeout|tabsScroll|tabs_slidePrev|tabs_slideNext|tabs_hideButtons|Math|isPause|cookie|animSpeed|tabs_setIsAnim|webkitTransitionEnd|isLast|stExtend|classTabsList|classViewsContainer|classBtnPrev|classBtnNext|isChild|slideH|slideV|baseEl|split|location|st_li_first|st_tab_first|WH|nextBtnH|slugCount|tabs_setUniqueSlug|tabsTotWH|tabsShowHash|mousewheel|classExtLink|rel|tabs_findByRel|resizeTimer|unalignedLi|alignedTop|alignedBottom|abs|content_init|300|classBtnDisabled|proccessing|onTabClick|tabs_load|tabs_show|html|onAjaxComplete|remove|prevTab|tabs_find|nextTab|tabs_removeActive|tabs_setFirstActive|tabs_saveActive|aD|onTabPrevSlide|onTabNextSlide|ms|timing|tabsEasing|content_positionContent|show|viewInner|content_getHeight|removeAttr|style|content_setSlideValues|contentEasing|isNext|content_bindTouch|unmousewheel|content_unbindTouch|classTabsContainer|isParent|fragmentId|tabs_getSlug|target|prepend|ontouchstart|chrome|indexOf|safari|WebKitCSSMatrix|new|clientX|marginLeft|clientY|marginTop|buttonsH|resize_width|resize_height|tabs_init|autoplay_init|prototype|100|buttonsVisible|tabs_posActive|tabs_bind|responsive|clearTimeout|tabs_setAutoWidth|tabs_setAutoHeight|tabs_|externalLinking|tabsContW|tabs_getActive|tabs_setActivePos|tabs_showAppended|display|block|tabs_setActive|isSwipe|tabs_slideClicked|autoplayClickStop|ajaxSpinner|span|st_spinner|ajaxCache|ajaxError|content_bindWebKitCallback|content_|tabs_clickPrev|tabs_clickNext|urlLinking|tabsSaveState|nextElemPos|isFirst|tabs_bindWebKitCallback|child|layerX|layerY|wkValArray|bind|autoplay_|owh|content_showActive|content_reInit|oldCSS|not|content_setParentsHeight|content_resetAutoHeight|fadeIn|fadeOut|easeOutSine|intervalId|autoplay_nextTab|autoplayInterval|autoplay_pause|autoplay_resume|extend|tabs_unbindTouch|defaults|ul|doc|document|toString|base|test|add|st_view_first|toLowerCase|opera|msie|compatible|mozilla|rv|navigator|userAgent|m11|st_webkit|overflow|vertical|tabsContWH|tabsOH|_|u00c0|uFFFF|limitXY|resize|Prev|Next|delegate|200|activeElemP|abort|delete|ajax|url|dataType|success|removeData|error|complete|pause|static|substring|slice|round|tabs_fixE|event|offsetX|offsetY|tabs_WebKitPosition|getComputedStyle|get|getPropertyValue|matrix|isNaN|dist|content_fade|content_fadeOutIn|content_slideBackRePos|content_slideH|content_slideV|setInterval|clearInterval|addTab|st_tab_|removeTab|goTo|goToPrev|goToNext|slidePrev|slideNext|setOptions|content_setContentHeight|tabs_bindTouch|setContentHeight|pauseAutoplay|resumeAutoplay|destroy|undelegate|getSettings|stCore|push|Failed|to|5000|classAutoplayCont|st_autoplay|st_btn_disabled|st_next|st_prev|st_ext|classNoTouch|st_no_touch|st_tab|st_tab_active|st_li_active|st_sliding_active|st_tabs|st_tabs_ul|st_view|st_view_active|st_view_inner|st_views|classViewsInner|st_views_wrap|600|easeInOutExpo|2560|jQuery'.split('|'),0,{}))
;/*
 * SlideTabs - Touch Extension
 *
 * @version 1.0
 * 
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(9($){$.2t($.1z,{1O:9(){6(2.h.1c){2.1P();2.4.13=0+2.p.2u;2.1Q();2.4.P=8;2.4.l=0}},1P:9(){i a=2.2v(),1R=(a-2.h.1S);2.4.D=-1R-2.p.1T},1Q:9(){i a=2;a.$a.w(\'1U\').E(\'1U\',9(){g 8});a.$1V.w(\'S\').E(\'S\',9(e){a.1W(e)})},2w:9(){2.$1V.w(\'S\')},1W:9(a){6(2.4.P){a.14();g 8}i b=2,F=a.1d.1e;6(F&&F.G==1){b.e=F[0]}j{g 8}b.$T.E(\'1A\',9(e){b.1X(e)});b.$T.E(\'1B\',9(e){b.1Y(e)});6(b.h.H){b.$4.7(\'-q-r-B\',\'0\')}b.4.Q=b.4.U=b.e[b.h.1f];6(b.h.H){b.4.o=b.1C(b.$4,b.h.1D)}j{b.4.o=15(b.$4.7(b.h.7))}b.4.V=b.4.Q-b.4.o+b.4.D;b.4.1g=b.4.V+b.4.13-b.4.D;b.4.W=b.4.o;b.4.I=X.Y();g 8},1X:9(a){a.14();i b=a.1d.1e;6(b.G>1){g 8}2.e=b[0];i c=2.4.R=2.e[2.h.1f];c=k.Z(c,2.4.V);c=k.1h(c,2.4.1g);2.4.J=2.4.K;2.4.l=(c-2.4.Q);6(2.4.J!=2.4.l){2.4.K=2.4.l}2.4.10=2.4.o+2.4.l;6(k.u(2.4.R-2.4.Q)>0){2.16(s,\'2x\')}2.$4.7(2.h.7,2.h.v+2.4.10+2.h.m);i d=X.Y();6(d-2.4.I>1Z){2.4.I=d;2.4.W=2.4.o+2.4.l}g 8},1Y:9(a){2.$T.w(\'1A\').w(\'1B\');6(2.h.H){2.4.t=2.1C(2.$4,2.h.1D)}j{2.4.t=15(2.$4.7(2.h.7))}2.4.t=(17(2.4.t))?0:2.4.t;2.11=k.u(2.4.t);i b=2,l=k.u(2.4.l);6(l==0){6(2.11==0+2.p.1T){1i(9(){b.4.P=8},1j)}j 6(2.11==k.u(2.4.D)){1i(9(){b.4.P=8},1j)}g 8}i c=k.Z(20,(X.Y())-2.4.I),1k=k.u(2.4.W-2.4.t),1l=1k/c,1m=k.u(2.h.1S-l);2.4.n=k.Z((1m)/1l,12);2.4.n=k.1h(2.4.n,21);2.4.n=(17(2.4.n))?22:2.4.n;6(2.11==0){6(2.p.23==\'24\'&&!2.p.25){2.26(2.$1n);2.27(2.$1o)}1i(9(){b.16(8,\'L\')},1j);g 8}j 6(2.11==k.u(2.4.D)){6(2.p.23==\'24\'&&!2.p.25){2.26(2.$1o);2.27(2.$1n)}1i(9(){b.16(8,\'L\')},1j);g 8}6(l>2y){6(2.4.U>2.4.R){6(2.4.J<2.4.K){2.18(2.4.n);g 8}2.2z(2.4.n)}j 6(2.4.U<2.4.R){6(2.4.J>2.4.K){2.18(2.4.n);g 8}2.2A(2.4.n)}j{2.18(12)}}j{2.18(12)}2.4.l=0;g 8},18:9(a){i b=2;6(b.h.H){b.2B();b.$4.7({\'-q-r-B\':a+\'1p\',\'-q-r-1q-9\':\'1r-1s\'}).7(b.h.7,b.h.v+b.4.o+b.h.m)}j{6(b.p.2C==\'2D\'){b.$4.M({\'2E\':b.4.o+\'m\'},a,\'N\',9(){b.16(8,\'L\')})}j{b.$4.M({\'2F\':b.4.o+\'m\'},a,\'N\',9(){b.16(8,\'L\')})}}b.11=k.u(b.4.o)},28:9(){6(2.h.1c){6(2.$a.G>1&&2.3.2G){2.3.1c=s;2.3.1t=\'S\';2.3.1E=\'1A\';2.3.1F=\'1B\';2.3.2H=\'2I\';2.29()}}},29:9(){i b=2;b.$1u.2J(\'.\'+b.p.2K).w(\'2a\').w(\'S\').E(\'2a S\',9(a){a.2L()});b.$2b.w(2.3.1t).E(2.3.1t,9(e){b.2c(e)})},2M:9(){2.3.1c=8;2.$2b.w(2.3.1t)},19:9(a){2.3.P=s;i b=2;6(b.h.H){b.2N(s);b.$z.7({\'-q-r-B\':a+\'1p\',\'-q-r-1q-9\':\'1r-1s\'}).7(b.3.7,b.3.v+\'0\'+b.3.m);b.$A.7({\'-q-r-B\':a+\'1p\',\'-q-r-1q-9\':\'1r-1s\'}).7(b.3.7,b.3.v+ -b.3.O+b.3.m);b.$C.7({\'-q-r-B\':a+\'1p\',\'-q-r-1q-9\':\'1r-1s\'}).7(b.3.7,b.3.v+b.3.O+b.3.m)}j{6(b.p.2d==\'2O\'){b.$A.M({\'1G\':-b.3.O+\'m\'},a,\'N\');b.$C.M({\'1G\':b.3.O+\'m\'},a,\'N\');b.$z.M({\'1G\':\'2e\'},a,\'N\',9(){b.1v(8,\'L\');b.1w()})}j{b.$A.M({\'1H\':-b.3.O+\'m\'},a,\'N\');b.$C.M({\'1H\':b.3.O+\'m\'},a,\'N\');b.$z.M({\'1H\':\'2e\'},a,\'N\',9(){b.1v(8,\'L\');b.1w()})}}},1w:9(){6(2.h.H){2.$A.7(\'-q-r-B\',\'2f\');2.$C.7(\'-q-r-B\',\'2f\')}2.$A.7(2.3.7,2.3.v+2.p.2g+2.3.m);2.$C.7(2.3.7,2.3.v+2.p.2g+2.3.m)},2c:9(a){6(2.3.P||2.4.P||2.4.2P){g 8}6(2.3.1I){g 8}i b=2,F=a.1d.1e;6(F&&F.G>0){2.e=F[0];2.3.1I=s}j{g 8}6(2.p.2Q==s){2.2R(8)}2.3.1a=8;2.$T.E(2.3.1E,9(e){b.2h(e)});2.$T.E(2.3.1F,9(e){b.1b(e)});2.$A=2.$z.1n(\'2i\');2.$C=2.$z.1o(\'2i\');i c=15(b.$1u.7(2.3.1J));2.3.D=-c;2.3.13=c;2.3.1K=-2.$A[2.3.2S](8);2.3.1L=2.$1u[2.3.1J]();6(2.h.H){2.$z.7(\'-q-r-B\',\'0\');2.$A.7(\'-q-r-B\',\'0\');2.$C.7(\'-q-r-B\',\'0\')}2.3.2j=2.e.2k;2.3.2l=2.e.2m;2.3.Q=2.3.U=2.e[2.3.1f];2.3.o=15(2.$z.7(2.3.7));2.3.o=(17(2.3.o))?0:2.3.o;2.3.V=2.3.Q-2.3.o+2.3.D;2.3.1g=2.3.V+2.3.13-2.3.D;2.3.W=2.3.o;2.3.I=X.Y()},1M:9(){2.$z.7(2.3.7,2.3.v+0+2.3.m);2.$A.7(2.3.7,2.3.v+2.3.1K+2.3.m);2.$C.7(2.3.7,2.3.v+2.3.1L+2.3.m);2.1v(8,\'L\')},2n:9(e,a){i b=(k.u(e.2k-2.3.2j)-k.u(e.2m-2.3.2l))-(a?-5:5);6(b>5){g\'x\'}j 6(b<-5){g\'y\'}},2h:9(a){6(2.3.1x){g}i b=a.1d.1e;6(b.G>1){2.1b(a);g}j{2.e=b[0]}6(!2.3.1a){i c=(2.p.2d==\'2T\')?s:8,1N=2.2n(2.e,c);6(1N==\'x\'){6(c){a.14()}j{2.3.1x=s;2.1b(2.e,s)}2.3.1a=s}j 6(1N==\'y\'){6(c){2.3.1x=s;2.1b(2.e,s)}j{a.14()}2.3.1a=s}g}a.14();i d=2.3.R=2.e[2.3.1f];d=k.Z(d,2.3.V);d=k.1h(d,2.3.1g);2.3.J=2.3.K;2.3.l=(d-2.3.Q);6(2.3.J!=2.3.l){2.3.K=2.3.l}6(!2.$A.G){6(2.3.l>0){2.1M();g 8}}j 6(!2.$C.G){6(2.3.l<0){2.1M();g 8}}2.3.10=2.3.o+2.3.l;i e=2.3.10+2.3.1K,2o=2.3.10+2.3.1L;2.$z.7(2.3.7,2.3.v+2.3.10+2.3.m);2.$A.7(2.3.7,2.3.v+e+2.3.m);2.$C.7(2.3.7,2.3.v+2o+2.3.m);i f=X.Y();6(f-2.3.I>1Z){2.3.I=f;2.3.W=2.3.o+2.3.l}},1b:9(a,b){2.$T.w(2.3.1E).w(2.3.1F);2.3.1I=8;2.3.1x=8;2.3.1a=8;6(b){g}2.3.O=2.$1u[2.3.1J]();i c=k.u(2.3.l),t;6(2.h.H){t=2.1C(2.$z,2.3.1D)}j{t=15(2.$z.7(2.3.7))}t=(17(t))?0:t;6(c==0||t==0){2.1w();g 8}i d=k.Z(20,(X.Y())-2.3.I),1k=k.u(2.3.W-2.3.l),1l=1k/d,1m=k.u(2.3.O-c);i e=2;2.3.n=k.Z((1m)/1l,12);2.3.n=k.1h(2.3.n,21);2.3.n=(17(2.3.n))?22:2.3.n;6(c>2U){i f;6(2.3.U>2.3.R){6(2.3.J<2.3.K){2.19(2.3.n);g 8}f=2.$2p.2q(\'1y\').1o(\'1y\').2r(\'a\')}j 6(2.3.U<2.3.R){6(2.3.J>2.3.K){2.19(2.3.n);g 8}f=2.$2p.2q(\'1y\').1n(\'1y\').2r(\'a\')}6(f&&f.G){2.2V(f,s,2.3.n)}j{2.19(12)}6(c==2.3.13){2.1v(8,\'L\');2.2W()}}j{2.19(12)}2.3.l=0;g 8}});$.2s.2X=$.1z.1O;$.2s.2Y=$.1z.28})(2Z);',62,186,'||this|content|tabs||if|css|false|function|||||||return|val|var|else|Math|dist|px|swipeSpeed|startXY|conf|webkit|transition|true|endXY|abs|pre|unbind|||currentView|prevView|duration|nextView|minXY|bind|te|length|useWebKit|startTs|lastPos|currPos|resume|animate|easeOutSine|slideLength|isAnim|eXY|end|touchstart|doc|start|minMouseXY|acc|Date|now|max|newXY|margin|200|maxXY|preventDefault|parseInt|tabs_setIsAnim|isNaN|tabs_slideBack|content_slideBack|dirCheck|content_touchEnd|isTouch|originalEvent|touches|clientXY|maxMouseXY|min|setTimeout|100|accDist|speed|subtDist|prev|next|ms|timing|ease|out|startEvent|contentCont|content_setIsAnim|content_slideBackRePos|dirBlock|li|stCore|touchmove|touchend|tabs_WebKitPosition|arrPos|moveEvent|endEvent|top|left|isMoving|wh|prevViewWH|nextViewWH|content_touchMoveReturn|dir|tabs_initTouch|tabs_setSwipeLength|tabs_bindTouch|limitXY|tabsSlideLength|offsetTL|dragstart|tabsInnerCont|tabs_touchStart|tabs_touchMove|tabs_touchEnd|350|40|600|300|buttonsFunction|slide|tabsLoop|tabs_disableButton|tabs_enableButton|content_initTouch|content_bindTouch|mousedown|views|content_touchStart|contentAnim|0px|0ms|viewportOffset|content_touchMove|div|eX|pageX|eY|pageY|content_touchDir|nextXY|tab|parent|children|stExtend|extend|offsetBR|tabs_getTotalLength|tabs_unbindTouch|pause|30|tabs_slideNext|tabs_slidePrev|tabs_bindWebKitCallback|orientation|horizontal|marginLeft|marginTop|animIsSlide|cancelEvent|touchcancel|find|classNoTouch|stopImmediatePropagation|content_unbindTouch|content_bindWebKitCallback|slideV|xhr|autoplay|autoplay_pause|owh|slideH|60|tabs_click|content_rePositionView|tabsTouch|contentTouch|jQuery'.split('|'),0,{}))