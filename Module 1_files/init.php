		var siteDomain, siteQueryString, scrollIframeToTop = false;
		var pageContentQuery;
		window.addEventListener("message", function (event) {
			//This data may be coming from other postMessages.  So we do a try and see if it parses our json format and if not no biggie.  If it does we then use the .message variable to check what function is being called.  Never go more variables deep than .message or it might throw errors.
			var data = new Object();
			try {
				data = JSON.parse(event.data);
			} catch (e) {
				data = event.data;
			}
			
			if (data.message == "setDomain") {
				siteDomain = data.domain;
			}

			if (data.message == "rsScroll" && $(window).width() < 700 && !scrollIframeToTop) {
				window.scrollTo(0, $("#rsIframe").offset().top);
			}
			;

			if (data.message == "scrollIframeToTop") {
				var contactTopPosition = $("#rsIframe").parent().position().top;
				$("#rsIframe").parent().scrollTop(contactTopPosition);
				scrollIframeToTop = true;
			}

			if (data.message == "disableOnBeforeUnload") {
				window.onbeforeunload = null;
				top.location = data.url;
			}

			if (data.message == "rsPageChange") {
				// Hide page content on form start
				$(pageContentQuery).hide();
				history.pushState([data.to, data.from], null, location.href);

				if (data.to == 1) {
					$(pageContentQuery).show();
				}

				if ((data.hasOwnProperty('direction') && data.direction === 'push')
					|| (data.to >= data.from && !data.hasOwnProperty('direction'))) {
					rsPageHistory.push([data.to, data.from]);
				} else {
					rsPageHistory.pop();
				}
			}

			if (data.message == "iframeModal") {
					iframeLoadedResize();
				if (data.style) {
					$("<style>" + data.style + "</style>").appendTo("head");
				}
				if (data.modalClose) {
					closeIframeModal();
				}
				if (typeof data.modalOn !== "undefined") {
					iframeToggle = data.modalOn;
				}
			}

			if (data.message == "iframeResizeOnLoadCheck") {
				iFrameResize();
			}

			if (data.message == "noShortTermModal") {
				const paragraph = $("<p>")
					.text('Customers typically find great success with short term lending products, the offers are not obligatory, so feel free to check out what you qualify for.');

				const button = $("<button>")
					.attr('type', 'button')
					.attr('onclick', 'rsCloseModal()')
					.text('Look at the Offers');

				const link = $("<a>")
					.attr('onclick', 'rsNoThankYou()')
					.attr('href', 'javascript:void(0);')
					.text('No thank you');

				const content = $("<div>")
					.attr('id', 'noShortTermModal')
					.append(paragraph)
					.append(button)
					.append(link);

				let styleText = '#noShortTermModal {max-width:400px;text-align:center;}';
				styleText += '#noShortTermModal p {margin-top:0;}';
				styleText += '#noShortTermModal button {background: royalblue;color: white;padding:10px 20px;margin-bottom:10px;border:2px solid dimgrey;width:100%;cursor:pointer;}';
				styleText += '#noShortTermModal a {display:block;color:lightgrey;text-decoration:none;}';
				const style = $("<style>").text(styleText);
				$("head").append(style);

				rsBuildModal(content);
			}

			if (data.message == 'removeListeners') {
				if (typeof window['on' + data.listener] !== 'undefined' && window['on' + data.listener] !== null) {
					window['on' + data.listener] = null;
				}
				$(window).unbind(data.listener);
			}
		});

		function initForm() {
						// <script> //
			//If page is already loaded
			if (document.readyState && /complete|interactive|loaded/.test(document.readyState)) {
				// In case the document has finished parsing, document's readyState will
				// be one of "complete", "interactive" or (non-standard) "loaded".
				addForm();
			}
			// Mozilla, Opera and webkit nightlies currently support this event
			else if (document.addEventListener) {
				// Use the handy event callback
				document.addEventListener("DOMContentLoaded", function () {
					addForm();
				});
				// If IE event model is used
			} else if (document.attachEvent) {
				// ensure firing before onload,
				// maybe late but safe also for iframes
				document.attachEvent("onreadystatechange", function () {
					addForm();
				});
			}
		}

		initForm();

			/* <script> */
		// if no jquery pulll it in
		if (typeof jQuery == 'undefined') {
			var script = document.createElement('script');
			script.type = "text/javascript";
			script.src = "https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js";
			script.onload = loadIframeStuff;

			// because IE just HAS to be different
			script.onreadystatechange = function () {
				if (script.readyState == 'loaded' || script.readyState == 'complete') {
					loadIframeStuff();
				}
			}
			document.getElementsByTagName('head')[0].appendChild(script);
		}

		/*! iFrame Resizer (iframeSizer.min.js ) - v3.5.5 - 2016-06-16
	 *  Desc: Force cross domain iframes to size to content.
	 *  Requires: iframeResizer.contentWindow.min.js to be loaded into the target frame.
	 *  Copyright: (c) 2016 David J. Bradshaw - dave@bradshaw.net
	 *  License: MIT
	 */

		!function (a) {
			"use strict";

			function b(b, c, d) {
				"addEventListener" in a ? b.addEventListener(c, d, !1) : "attachEvent" in a && b.attachEvent("on" + c, d)
			}

			function c(b, c, d) {
				"removeEventListener" in a ? b.removeEventListener(c, d, !1) : "detachEvent" in a && b.detachEvent("on" + c, d)
			}

			function d() {
				var b, c = ["moz", "webkit", "o", "ms"];
				for (b = 0; b < c.length && !N; b += 1) N = a[c[b] + "RequestAnimationFrame"];
				N || h("setup", "RequestAnimationFrame not supported")
			}

			function e(b) {
				var c = "Host page: " + b;
				return a.top !== a.self && (c = a.parentIFrame && a.parentIFrame.getId ? a.parentIFrame.getId() + ": " + b : "Nested host page: " + b), c
			}

			function f(a) {
				return K + "[" + e(a) + "]"
			}

			function g(a) {
				return P[a] ? P[a].log : G
			}

			function h(a, b) {
				k("log", a, b, g(a))
			}

			function i(a, b) {
				k("info", a, b, g(a))
			}

			function j(a, b) {
				k("warn", a, b, !0)
			}

			function k(b, c, d, e) {
				!0 === e && "object" == typeof a.console && console[b](f(c), d)
			}

			function l(d) {
				function e() {
					function a() {
						s(V), p(W)
					}

					g("Height"), g("Width"), t(a, V, "init")
				}

				function f() {
					var a = U.substr(L).split(":");
					return {iframe: P[a[0]].iframe, id: a[0], height: a[1], width: a[2], type: a[3]}
				}

				function g(a) {
					var b = Number(P[W]["max" + a]), c = Number(P[W]["min" + a]), d = a.toLowerCase(), e = Number(V[d]);
					h(W, "Checking " + d + " is in range " + c + "-" + b), c > e && (e = c, h(W, "Set " + d + " to min value")), e > b && (e = b, h(W, "Set " + d + " to max value")), V[d] = "" + e
				}

				function k() {
					function a() {
						function a() {
							var a = 0, d = !1;
							for (h(W, "Checking connection is from allowed list of origins: " + c); a < c.length; a++) if (c[a] === b) {
								d = !0;
								break
							}
							return d
						}

						function d() {
							var a = P[W].remoteHost;
							return h(W, "Checking connection is from: " + a), b === a
						}

						return c.constructor === Array ? a() : d()
					}

					var b = d.origin, c = P[W].checkOrigin;
					if (c && "" + b != "null" && !a()) throw new Error("Unexpected message received from: " + b + " for " + V.iframe.id + ". Message was: " + d.data + ". This error can be disabled by setting the checkOrigin: false option or by providing of array of trusted domains.");
					return !0
				}

				function l() {
					return K === ("" + U).substr(0, L) && U.substr(L).split(":")[0] in P
				}

				function w() {
					var a = V.type in {"true": 1, "false": 1, undefined: 1};
					return a && h(W, "Ignoring init message from meta parent page"), a
				}

				function y(a) {
					return U.substr(U.indexOf(":") + J + a)
				}

				function z(a) {
					h(W, "MessageCallback passed: {iframe: " + V.iframe.id + ", message: " + a + "}"), N("messageCallback", {
						iframe: V.iframe,
						message: JSON.parse(a)
					}), h(W, "--")
				}

				function A() {
					var b = document.body.getBoundingClientRect(), c = V.iframe.getBoundingClientRect();
					return JSON.stringify({
						iframeHeight: c.height,
						iframeWidth: c.width,
						clientHeight: Math.max(document.documentElement.clientHeight, a.innerHeight || 0),
						clientWidth: Math.max(document.documentElement.clientWidth, a.innerWidth || 0),
						offsetTop: parseInt(c.top - b.top, 10),
						offsetLeft: parseInt(c.left - b.left, 10),
						scrollTop: a.pageYOffset,
						scrollLeft: a.pageXOffset
					})
				}

				function B(a, b) {
					function c() {
						u("Send Page Info", "pageInfo:" + A(), a, b)
					}

					x(c, 32)
				}

				function C() {
					function d(b, c) {
						function d() {
							P[g] ? B(P[g].iframe, g) : e()
						}

						["scroll", "resize"].forEach(function (e) {
							h(g, b + e + " listener for sendPageInfo"), c(a, e, d)
						})
					}

					function e() {
						d("Remove ", c)
					}

					function f() {
						d("Add ", b)
					}

					var g = W;
					f(), P[g].stopPageInfo = e
				}

				function D() {
					P[W] && P[W].stopPageInfo && (P[W].stopPageInfo(), delete P[W].stopPageInfo)
				}

				function E() {
					var a = !0;
					return null === V.iframe && (j(W, "IFrame (" + V.id + ") not found"), a = !1), a
				}

				function F(a) {
					var b = a.getBoundingClientRect();
					return o(W), {
						x: Math.floor(Number(b.left) + Number(M.x)),
						y: Math.floor(Number(b.top) + Number(M.y))
					}
				}

				function G(b) {
					function c() {
						M = g, H(), h(W, "--")
					}

					function d() {
						return {x: Number(V.width) + f.x, y: Number(V.height) + f.y}
					}

					function e() {
						a.parentIFrame ? a.parentIFrame["scrollTo" + (b ? "Offset" : "")](g.x, g.y) : j(W, "Unable to scroll to requested position, window.parentIFrame not found")
					}

					var f = b ? F(V.iframe) : {x: 0, y: 0}, g = d();
					h(W, "Reposition requested from iFrame (offset x:" + f.x + " y:" + f.y + ")"), a.top !== a.self ? e() : c()
				}

				function H() {
					!1 !== N("scrollCallback", M) ? p(W) : q()
				}

				function I(b) {
					function c() {
						var a = F(g);
						h(W, "Moving to in page link (#" + e + ") at x: " + a.x + " y: " + a.y), M = {
							x: a.x,
							y: a.y
						}, H(), h(W, "--")
					}

					function d() {
						a.parentIFrame ? a.parentIFrame.moveToAnchor(e) : h(W, "In page link #" + e + " not found and window.parentIFrame not found")
					}

					var e = b.split("#")[1] || "", f = decodeURIComponent(e),
						g = document.getElementById(f) || document.getElementsByName(f)[0];
					g ? c() : a.top !== a.self ? d() : h(W, "In page link #" + e + " not found")
				}

				function N(a, b) {
					return m(W, a, b)
				}

				function O() {
					switch (P[W].firstRun && T(), V.type) {
						case"close":
							n(V.iframe);
							break;
						case"message":
							z(y(6));
							break;
						case"scrollTo":
							G(!1);
							break;
						case"scrollToOffset":
							G(!0);
							break;
						case"pageInfo":
							B(P[W].iframe, W), C();
							break;
						case"pageInfoStop":
							D();
							break;
						case"inPageLink":
							I(y(9));
							break;
						case"reset":
							r(V);
							break;
						case"init":
							e(), N("initCallback", V.iframe), N("resizedCallback", V);
							break;
						default:
							e(), N("resizedCallback", V)
					}
				}

				function Q(a) {
					var b = !0;
					return P[a] || (b = !1, j(V.type + " No settings for " + a + ". Message was: " + U)), b
				}

				function S() {
					for (var a in P) u("iFrame requested init", v(a), document.getElementById(a), a)
				}

				function T() {
					P[W].firstRun = !1
				}

				var U = d.data, V = {}, W = null;
				"[iFrameResizerChild]Ready" === U ? S() : l() ? (V = f(), W = R = V.id, !w() && Q(W) && (h(W, "Received: " + U), E() && k() && O())) : i(W, "Ignored: " + U)
			}

			function m(a, b, c) {
				var d = null, e = null;
				if (P[a]) {
					if (d = P[a][b], "function" != typeof d) throw new TypeError(b + " on iFrame[" + a + "] is not a function");
					e = d(c)
				}
				return e
			}

			function n(a) {
				var b = a.id;
				h(b, "Removing iFrame: " + b), a.parentNode.removeChild(a), m(b, "closedCallback", b), h(b, "--"), delete P[b]
			}

			function o(b) {
				null === M && (M = {
					x: void 0 !== a.pageXOffset ? a.pageXOffset : document.documentElement.scrollLeft,
					y: void 0 !== a.pageYOffset ? a.pageYOffset : document.documentElement.scrollTop
				}, h(b, "Get page position: " + M.x + "," + M.y))
			}

			function p(b) {
				null !== M && (a.scrollTo(M.x, M.y), h(b, "Set page position: " + M.x + "," + M.y), q())
			}

			function q() {
				M = null
			}

			function r(a) {
				function b() {
					s(a), u("reset", "reset", a.iframe, a.id)
				}

				h(a.id, "Size reset requested by " + ("init" === a.type ? "host page" : "iFrame")), o(a.id), t(b, a, "reset")
			}

			function s(a) {
				function b(b) {
					a.iframe.style[b] = a[b] + "px", h(a.id, "IFrame (" + e + ") " + b + " set to " + a[b] + "px")
				}

				function c(b) {
					H || "0" !== a[b] || (H = !0, h(e, "Hidden iFrame detected, creating visibility listener"), y())
				}

				function d(a) {
					b(a), c(a)
				}

				var e = a.iframe.id;
				P[e] && (P[e].sizeHeight && d("height"), P[e].sizeWidth && d("width"))
			}

			function t(a, b, c) {
				c !== b.type && N ? (h(b.id, "Requesting animation frame"), N(a)) : a()
			}

			function u(a, b, c, d) {
				function e() {
					var e = P[d].targetOrigin;
					h(d, "[" + a + "] Sending msg to iframe[" + d + "] (" + b + ") targetOrigin: " + e), c.contentWindow.postMessage(K + b, "*")
				}

				function f() {
					i(d, "[" + a + "] IFrame(" + d + ") not found"), P[d] && delete P[d]
				}

				function g() {
					c && "contentWindow" in c && null !== c.contentWindow ? e() : f()
				}

				d = d || c.id, P[d] && g()
			}

			function v(a) {
				return a + ":" + P[a].bodyMarginV1 + ":" + P[a].sizeWidth + ":" + P[a].log + ":" + P[a].interval + ":" + P[a].enablePublicMethods + ":" + P[a].autoResize + ":" + P[a].bodyMargin + ":" + P[a].heightCalculationMethod + ":" + P[a].bodyBackground + ":" + P[a].bodyPadding + ":" + P[a].tolerance + ":" + P[a].inPageLinks + ":" + P[a].resizeFrom + ":" + P[a].widthCalculationMethod
			}

			function w(a, c) {
				function d() {
					function b(b) {
						1 / 0 !== P[w][b] && 0 !== P[w][b] && (a.style[b] = P[w][b] + "px", h(w, "Set " + b + " = " + P[w][b] + "px"))
					}

					function c(a) {
						if (P[w]["min" + a] > P[w]["max" + a]) throw new Error("Value for min" + a + " can not be greater than max" + a)
					}

					c("Height"), c("Width"), b("maxHeight"), b("minHeight"), b("maxWidth"), b("minWidth")
				}

				function e() {
					var a = c && c.id || S.id + F++;
					return null !== document.getElementById(a) && (a += F++), a
				}

				function f(b) {
					return R = b, "" === b && (a.id = b = e(), G = (c || {}).log, R = b, h(b, "Added missing iframe ID: " + b + " (" + a.src + ")")), b
				}

				function g() {
					h(w, "IFrame scrolling " + (P[w].scrolling ? "enabled" : "disabled") + " for " + w), a.style.overflow = !1 === P[w].scrolling ? "hidden" : "auto", a.scrolling = !1 === P[w].scrolling ? "no" : "yes"
				}

				function i() {
					("number" == typeof P[w].bodyMargin || "0" === P[w].bodyMargin) && (P[w].bodyMarginV1 = P[w].bodyMargin, P[w].bodyMargin = "" + P[w].bodyMargin + "px")
				}

				function k() {
					var b = P[w].firstRun, c = P[w].heightCalculationMethod in O;
					!b && c && r({iframe: a, height: 0, width: 0, type: "init"})
				}

				function l() {
					Function.prototype.bind && (P[w].iframe.iFrameResizer = {
						close: n.bind(null, P[w].iframe),
						resize: u.bind(null, "Window resize", "resize", P[w].iframe),
						moveToAnchor: function (a) {
							u("Move to anchor", "moveToAnchor:" + a, P[w].iframe, w)
						},
						sendMessage: function (a) {
							a = JSON.stringify(a), u("Send Message", "message:" + a, P[w].iframe, w)
						}
					})
				}

				function m(c) {
					function d() {
						u("iFrame.onload", c, a), k()
					}

					b(a, "load", d), u("init", c, a)
				}

				function o(a) {
					if ("object" != typeof a) throw new TypeError("Options is not an object")
				}

				function p(a) {
					for (var b in S) S.hasOwnProperty(b) && (P[w][b] = a.hasOwnProperty(b) ? a[b] : S[b])
				}

				function q(a) {
					return "" === a || "file://" === a ? "*" : a
				}

				function s(b) {
					b = b || {}, P[w] = {
						firstRun: !0,
						iframe: a,
						remoteHost: a.src.split("/").slice(0, 3).join("/")
					}, o(b), p(b), P[w].targetOrigin = !0 === P[w].checkOrigin ? q(P[w].remoteHost) : "*"
				}

				function t() {
					return w in P && "iFrameResizer" in a
				}

				var w = f(a.id);
				t() ? j(w, "Ignored iFrame, already setup.") : (s(c), g(), d(), i(), m(v(w)), l())
			}

			function x(a, b) {
				null === Q && (Q = setTimeout(function () {
					Q = null, a()
				}, b))
			}

			function y() {
				function b() {
					function a(a) {
						function b(b) {
							return "0px" === P[a].iframe.style[b]
						}

						function c(a) {
							return null !== a.offsetParent
						}

						c(P[a].iframe) && (b("height") || b("width")) && u("Visibility change", "resize", P[a].iframe, a)
					}

					for (var b in P) a(b)
				}

				function c(a) {
					h("window", "Mutation observed: " + a[0].target + " " + a[0].type), x(b, 16)
				}

				function d() {
					var a = document.querySelector("body"), b = {
						attributes: !0,
						attributeOldValue: !1,
						characterData: !0,
						characterDataOldValue: !1,
						childList: !0,
						subtree: !0
					}, d = new e(c);
					d.observe(a, b)
				}

				var e = a.MutationObserver || a.WebKitMutationObserver;
				e && d()
			}

			function z(a) {
				function b() {
					B("Window " + a, "resize")
				}

				h("window", "Trigger event: " + a), x(b, 16)
			}

			function A() {
				function a() {
					B("Tab Visable", "resize")
				}

				"hidden" !== document.visibilityState && (h("document", "Trigger event: Visiblity change"), x(a, 16))
			}

			function B(a, b) {
				function c(a) {
					return "parent" === P[a].resizeFrom && P[a].autoResize && !P[a].firstRun
				}

				for (var d in P) c(d) && u(a, b, document.getElementById(d), d)
			}

			function C() {
				b(a, "message", l), b(a, "resize", function () {
					z("resize")
				}), b(document, "visibilitychange", A), b(document, "-webkit-visibilitychange", A), b(a, "focusin", function () {
					z("focus")
				}), b(a, "focus", function () {
					z("focus")
				})
			}

			function D() {
				function a(a, c) {
					function d() {
						if (!c.tagName) throw new TypeError("Object is not a valid DOM element");
						if ("IFRAME" !== c.tagName.toUpperCase()) throw new TypeError("Expected <IFRAME> tag, found <" + c.tagName + ">")
					}

					c && (d(), w(c, a), b.push(c))
				}

				var b;
				return d(), C(), function (c, d) {
					switch (b = [], typeof d) {
						case"undefined":
						case"string":
							Array.prototype.forEach.call(document.querySelectorAll(d || "iframe"), a.bind(void 0, c));
							break;
						case"object":
							a(c, d);
							break;
						default:
							throw new TypeError("Unexpected data type (" + typeof d + ")")
					}
					return b
				}
			}

			function E(a) {
				a.fn ? a.fn.iFrameResize = function (a) {
					function b(b, c) {
						w(c, a)
					}

					return this.filter("iframe").each(b).end()
				} : i("", "Unable to bind to jQuery, it is not fully loaded.")
			}

			var F = 0, G = !1, H = !1, I = "message", J = I.length, K = "[iFrameSizer]", L = K.length, M = null,
				N = a.requestAnimationFrame, O = {max: 1, scroll: 1, bodyScroll: 1, documentElementScroll: 1}, P = {},
				Q = null, R = "Host Page", S = {
					autoResize: !0,
					bodyBackground: null,
					bodyMargin: null,
					bodyMarginV1: 8,
					bodyPadding: null,
					checkOrigin: !0,
					inPageLinks: !1,
					enablePublicMethods: !0,
					heightCalculationMethod: "bodyOffset",
					id: "iFrameResizer",
					interval: 32,
					log: !1,
					maxHeight: 1 / 0,
					maxWidth: 1 / 0,
					minHeight: 0,
					minWidth: 0,
					resizeFrom: "parent",
					scrolling: !1,
					sizeHeight: !0,
					sizeWidth: !1,
					tolerance: 0,
					widthCalculationMethod: "scroll",
					closedCallback: function () {
					},
					initCallback: function () {
					},
					messageCallback: function () {
						j("MessageCallback function not defined")
					},
					resizedCallback: function () {
					},
					scrollCallback: function () {
						return !0
					}
				};
			a.jQuery && E(jQuery), "function" == typeof define && define.amd ? define([], D) : "object" == typeof module && "object" == typeof module.exports ? module.exports = D() : a.iFrameResize = a.iFrameResize || D()
		}(window || {});


		var rsPageHistory = [];
		rsPageHistory.push([1, 1]);


		var iframeParentWidth, iframeParentHeight, iframeParentMaxWidth, iframeTop;
		var iframeToggle = false;
		var tmpHtml = document.documentElement;

		function resizeIframeModalParent() {

			var documentHeight = Math.max(document.body.scrollHeight, document.body.offsetHeight,
				tmpHtml.clientHeight, tmpHtml.scrollHeight, tmpHtml.offsetHeight);

			var documentWidth = Math.max(document.body.scrollWidth, document.body.offsetWidth,
				tmpHtml.clientWidth, tmpHtml.scrollWidth, tmpHtml.offsetWidth);

			$("#iframeModalBackground").height(documentHeight);
			$(".iframeModalContainer").css({"height": "100%", "width": "100%"});
			$("#iframePlaceholder").css({"width": documentWidth});
		}


		function openIframeModal(iframeModalAdjustedHeight) {

			iframeTopDistance = $("#rsIframe").offset().top;

			iframeParentWidth = $("#rsIframe").parent().width() || $("#rsIframe").width();
			iframeParentHeight = $("#rsIframe").parent().height() || $("#rsIframe").height();
			iframeTop = $("#rsIframe").css("top") ? $("#rsIframe").css("top") : "";

			console.log(iframeParentHeight);

			var tmpHtml = document.documentElement;
			var documentHeight = Math.max(document.body.scrollHeight, document.body.offsetHeight,
				tmpHtml.clientHeight, tmpHtml.scrollHeight, tmpHtml.offsetHeight);
			$('body').css({"overflow": "hidden"});
			$('.container').css('max-width', '100%');
			if ($("#iframePlaceholder").length > 0) {
				$("#iframePlaceholder").remove();
				$("#iframeModalBackground").remove();
			}
			$("#rsIframe").parent().parent().append("<div id=\"iframePlaceholder\" style=\"height:" + iframeModalAdjustedHeight + "px;width:" + iframeParentWidth + "px;\">&nbsp;</div>");
			$("body").prepend("<div id=\"iframeModalBackground\" style=\"height:" + documentHeight + "px;\">&nbsp;</div>");
			$("#rsIframe").parent().addClass("iframeModalContainer").attr("scrolling", "yes");
			$("#rsIframe").addClass("iframeModal").css({"top": "50px", "left": "0"});
		}

		function closeIframeModal() {
			$(pageContentQuery).show();
			// $('body').css({"overflow-y": "scroll", "position": "relative"});
			$('body').css({"overflow-y": "scroll"});
			$('.container').css('max-width', '1200px');
			// $('body').unbind('touchmove');
			$("#iframePlaceholder").remove();
			$("#iframeModalBackground").remove();
			$("#rsIframe").parent().removeClass("iframeModalContainer").css({
				"max-width": iframeParentMaxWidth + "",
				"height": "",
				"width": ""
			});
			$("#rsIframe").removeClass("iframeModal").css({"top": "", "left": ""});
			iframeToggle = false;
		}


		var iframeLoadedResizeOn = false;

		function iframeLoadedResize() {
			if (!iframeLoadedResizeOn) {
				iframeLoadedResizeOn = true;
				iFrameResize({
					autoResize: true,
					enablePublicMethods: true,
					resizeFrom: "parent",
					tolerance: 5,
					minHeight: 300,
					log: false,
					heightCalculationMethod: 'bodyOffset',
					resizedCallback: function (messageData) {
						if (iframeToggle) {
							openIframeModal(messageData.height);
							resizeIframeModalParent();
						}
					},
				});
				console.log('iframe resizer ran');
			}
		}

		//In case browser doesnt support onload= on an iframe
		setTimeout(function () {
			iframeLoadedResize();
		}, 500);

		function loadIframeStuff() {
			$(function () {
				iframeParentMaxWidth = $("#rsIframe").parent().css("max-width") || $("#rsIframe").parent().width();
			})
			$(window).resize(function () {
				resizeIframeModalParent();
			});
			resizeIframeModalParent();
			var doubleCheckIframeModalResized = setInterval(function () {
				if (iframeToggle && $('#rsIframe').height() > 200) {
					openIframeModal($('#rsIframe').height());
					resizeIframeModalParent();
					clearInterval(doubleCheckIframeModalResized);
				}
			}, 100);
		}

		/**
		 * Set query string for hiding content
		 *
		 * @param elementList CSS Query String
		 */
		function setElementsForHiding(elementList) {
			if (typeof elementList === 'undefined') {
				elementList = '';
			}

			pageContentQuery = elementList;
		}

		
		function addForm() {
			var targetDiv = 'rsForm';
			var referrer = document.referrer ? encodeURIComponent(document.referrer) : '';
			var documentLocation = document.location.href ? encodeURIComponent(document.location.href) : '';

			var ajax_html_string_start = "<iframe id=\"rsIframe\" name=\"rsIframe\" src=\"https:\/\/www.rndframe.com\/server\/installmentStep.php?lang=en&lapr=0&style=STYLE1&ar=1&h=tx36E-xUdD8NO6kIAsf1yM1xcTzPAU-Qt68H4Z6at40.&subId=&subId2=&subId3=&domain=&userId=147238&rsaiOptimize=&rsaiUuid=&StepAmountSelect=BUTTONS&pref=";var ajax_html_string_end = "\" width=\"100%\"  frameborder=\"0\" autostyle=\"border: none;\" scrolling=\"no\" onload=\"iframeLoadedResize()\" referrerpolicy=\"always, no-referrer-when-downgrade, unsafe-url\"\/><\/iframe>";var ajax_html_string = ajax_html_string_start + referrer + "&prepop=" + documentLocation +  ajax_html_string_end;			var node = document.getElementById(targetDiv);
			// Set these styles to make sure the form is 100% acceptable
			// While we want to use as much height as we can get, only the overflow needs important
			var nodeOffset = node.offsetTop; // This offset top is important in case there is content in parent div above form
			node.setAttribute("style", "height:calc(100% - " + nodeOffset + "px);overflow:auto !important;");
			node.style.height = "calc(100% - " + nodeOffset + "px)";
			node.style.overflow = 'auto !important';
			// Load form
			node.innerHTML = ajax_html_string;


			

		}

		function showError() {
			var targetDiv = 'rsForm';
			var ajax_html_string = "";			document.getElementById(targetDiv).innerHTML = ajax_html_string;
		}

		
				history.pushState(null, null, location.href);
		
		window.addEventListener("popstate", function (event) {
			if (event.state === null) {
				console.log(event.state);
				return;
			}

			var iframeWin = document.getElementById("rsIframe").contentWindow;

			

			if (rsPageHistory.length > 1 && $(".iframeModalContainer").length === 0) {
				//get the last set of pages
				console.log(rsPageHistory.length);
				var currentPage = rsPageHistory[rsPageHistory.length - 1];
				iframeWin.postMessage('{"message":"rsPageChange","to":' + currentPage[1] + ',"from":' + currentPage[0] + ',"direction":"pop"}', "*");
				//H? history.pushState(null, null, location.href);
			} else if (typeof siteDomain !== 'undefined')//rs domains only
			{
				location.replace(location.href.substring(0, location.href.lastIndexOf("/") + 1) + "wait.php" + window.location.search);
			} else {
				history.back();
			}
		});
		
		let rsModalId;

		function rsBuildModal(content) {
			const thisDate = new Date();
			rsModalId = 'customModal' + thisDate.getTime();

			const modalContent = $("<div>")
				.addClass('content')
				.html(content);

			const modalBackground = $("<div>")
				.addClass('background')
				.attr('onclick', 'rsCloseModal()');

			const modalBox = $("<div>")
				.attr('id', rsModalId)
				.append(modalBackground)
				.append(modalContent);

			let styleText = '#' + rsModalId + ' {position:fixed;top:0;height:100%;width:100%;z-index:10000;}';
			styleText += '#' + rsModalId + ' .background {position:absolute;height:100%;width:100%;background-color:rgba(0, 0, 0, 0.75);}';
			styleText += '#' + rsModalId + ' .content {position:absolute;left:50%;top:50%;background-color:white;transform:translate(-50%, -50%);padding:30px;}';
			const style = $("<style>").text(styleText);
			$("head").append(style);

			$("body").append(modalBox);
		}

		function rsCloseModal() {
			$("#" + rsModalId).remove();
		}

		function rsNoThankYou() {
			const parentMessage = '{"message":"noShortTermModal"}';
			document.getElementById("rsIframe").contentWindow.postMessage(parentMessage, "*");
		}

