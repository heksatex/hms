!(
	/**
	 * Highcharts JS v11.4.0 (2024-03-04)
	 *
	 * (c) 2009-2024 Torstein Honsi
	 *
	 * License: www.highcharts.com/license
	 */ (function (t, e) {
		"object" == typeof module && module.exports
			? ((e.default = e), (module.exports = t && t.document ? e(t) : e))
			: "function" == typeof define && define.amd
			? define("highcharts/highcharts", function () {
					return e(t);
			  })
			: (t.Highcharts && t.Highcharts.error(16, !0), (t.Highcharts = e(t)));
	})("undefined" != typeof window ? window : this, function (t) {
		"use strict";
		var e = {};
		function i(e, i, s, r) {
			e.hasOwnProperty(i) ||
				((e[i] = r.apply(null, s)),
				"function" == typeof CustomEvent &&
					t.dispatchEvent(
						new CustomEvent("HighchartsModuleLoaded", {
							detail: { path: i, module: e[i] },
						})
					));
		}
		return (
			i(e, "Core/Globals.js", [], function () {
				var e, i;
				return (
					((i = e || (e = {})).SVG_NS = "http://www.w3.org/2000/svg"),
					(i.product = "Highcharts"),
					(i.version = "11.4.0"),
					(i.win = void 0 !== t ? t : {}),
					(i.doc = i.win.document),
					(i.svg =
						i.doc &&
						i.doc.createElementNS &&
						!!i.doc.createElementNS(i.SVG_NS, "svg").createSVGRect),
					(i.userAgent = (i.win.navigator && i.win.navigator.userAgent) || ""),
					(i.isChrome = -1 !== i.userAgent.indexOf("Chrome")),
					(i.isFirefox = -1 !== i.userAgent.indexOf("Firefox")),
					(i.isMS = /(edge|msie|trident)/i.test(i.userAgent) && !i.win.opera),
					(i.isSafari = !i.isChrome && -1 !== i.userAgent.indexOf("Safari")),
					(i.isTouchDevice = /(Mobile|Android|Windows Phone)/.test(
						i.userAgent
					)),
					(i.isWebKit = -1 !== i.userAgent.indexOf("AppleWebKit")),
					(i.deg2rad = (2 * Math.PI) / 360),
					(i.hasBidiBug =
						i.isFirefox && 4 > parseInt(i.userAgent.split("Firefox/")[1], 10)),
					(i.marginNames = [
						"plotTop",
						"marginRight",
						"marginBottom",
						"plotLeft",
					]),
					(i.noop = function () {}),
					(i.supportsPassiveEvents = (function () {
						let t = !1;
						if (!i.isMS) {
							let e = Object.defineProperty({}, "passive", {
								get: function () {
									t = !0;
								},
							});
							i.win.addEventListener &&
								i.win.removeEventListener &&
								(i.win.addEventListener("testPassive", i.noop, e),
								i.win.removeEventListener("testPassive", i.noop, e));
						}
						return t;
					})()),
					(i.charts = []),
					(i.composed = []),
					(i.dateFormats = {}),
					(i.seriesTypes = {}),
					(i.symbolSizes = {}),
					(i.chartCount = 0),
					e
				);
			}),
			i(e, "Core/Utilities.js", [e["Core/Globals.js"]], function (t) {
				let e;
				let { charts: i, doc: s, win: r } = t;
				function o(e, i, s, a) {
					let n = i ? "Highcharts error" : "Highcharts warning";
					32 === e && (e = `${n}: Deprecated member`);
					let h = p(e),
						l = h
							? `${n} #${e}: www.highcharts.com/errors/${e}/`
							: e.toString();
					if (void 0 !== a) {
						let t = "";
						h && (l += "?"),
							k(a, function (e, i) {
								(t += `
 - ${i}: ${e}`),
									h && (l += encodeURI(i) + "=" + encodeURI(e));
							}),
							(l += t);
					}
					M(
						t,
						"displayError",
						{ chart: s, code: e, message: l, params: a },
						function () {
							if (i) throw Error(l);
							r.console && -1 === o.messages.indexOf(l) && console.warn(l);
						}
					),
						o.messages.push(l);
				}
				function a(t, e) {
					return parseInt(t, e || 10);
				}
				function n(t) {
					return "string" == typeof t;
				}
				function h(t) {
					let e = Object.prototype.toString.call(t);
					return "[object Array]" === e || "[object Array Iterator]" === e;
				}
				function l(t, e) {
					return !!t && "object" == typeof t && (!e || !h(t));
				}
				function d(t) {
					return l(t) && "number" == typeof t.nodeType;
				}
				function c(t) {
					let e = t && t.constructor;
					return !!(l(t, !0) && !d(t) && e && e.name && "Object" !== e.name);
				}
				function p(t) {
					return "number" == typeof t && !isNaN(t) && t < 1 / 0 && t > -1 / 0;
				}
				function u(t) {
					return null != t;
				}
				function g(t, e, i) {
					let s;
					let r = n(e) && !u(i),
						o = (e, i) => {
							u(e)
								? t.setAttribute(i, e)
								: r
								? (s = t.getAttribute(i)) ||
								  "class" !== i ||
								  (s = t.getAttribute(i + "Name"))
								: t.removeAttribute(i);
						};
					return n(e) ? o(i, e) : k(e, o), s;
				}
				function f(t) {
					return h(t) ? t : [t];
				}
				function m(t, e) {
					let i;
					for (i in (t || (t = {}), e)) t[i] = e[i];
					return t;
				}
				function x() {
					let t = arguments,
						e = t.length;
					for (let i = 0; i < e; i++) {
						let e = t[i];
						if (null != e) return e;
					}
				}
				function y(t, e) {
					m(t.style, e);
				}
				function b(t) {
					return Math.pow(10, Math.floor(Math.log(t) / Math.LN10));
				}
				function v(t, e) {
					return t > 1e14 ? t : parseFloat(t.toPrecision(e || 14));
				}
				((o || (o = {})).messages = []),
					(Math.easeInOutSine = function (t) {
						return -0.5 * (Math.cos(Math.PI * t) - 1);
					});
				let S = Array.prototype.find
					? function (t, e) {
							return t.find(e);
					  }
					: function (t, e) {
							let i;
							let s = t.length;
							for (i = 0; i < s; i++) if (e(t[i], i)) return t[i];
					  };
				function k(t, e, i) {
					for (let s in t)
						Object.hasOwnProperty.call(t, s) && e.call(i || t[s], t[s], s, t);
				}
				function C(t, e, i) {
					function s(e, i) {
						let s = t.removeEventListener;
						s && s.call(t, e, i, !1);
					}
					function r(i) {
						let r, o;
						t.nodeName &&
							(e ? ((r = {})[e] = !0) : (r = i),
							k(r, function (t, e) {
								if (i[e]) for (o = i[e].length; o--; ) s(e, i[e][o].fn);
							}));
					}
					let o = ("function" == typeof t && t.prototype) || t;
					if (Object.hasOwnProperty.call(o, "hcEvents")) {
						let t = o.hcEvents;
						if (e) {
							let o = t[e] || [];
							i
								? ((t[e] = o.filter(function (t) {
										return i !== t.fn;
								  })),
								  s(e, i))
								: (r(t), (t[e] = []));
						} else r(t), delete o.hcEvents;
					}
				}
				function M(e, i, r, o) {
					if (
						((r = r || {}),
						s.createEvent && (e.dispatchEvent || (e.fireEvent && e !== t)))
					) {
						let t = s.createEvent("Events");
						t.initEvent(i, !0, !0),
							(r = m(t, r)),
							e.dispatchEvent ? e.dispatchEvent(r) : e.fireEvent(i, r);
					} else if (e.hcEvents) {
						r.target ||
							m(r, {
								preventDefault: function () {
									r.defaultPrevented = !0;
								},
								target: e,
								type: i,
							});
						let t = [],
							s = e,
							o = !1;
						for (; s.hcEvents; )
							Object.hasOwnProperty.call(s, "hcEvents") &&
								s.hcEvents[i] &&
								(t.length && (o = !0), t.unshift.apply(t, s.hcEvents[i])),
								(s = Object.getPrototypeOf(s));
						o && t.sort((t, e) => t.order - e.order),
							t.forEach((t) => {
								!1 === t.fn.call(e, r) && r.preventDefault();
							});
					}
					o && !r.defaultPrevented && o.call(e, r);
				}
				k(
					{
						map: "map",
						each: "forEach",
						grep: "filter",
						reduce: "reduce",
						some: "some",
					},
					function (e, i) {
						t[i] = function (t) {
							return (
								o(32, !1, void 0, { [`Highcharts.${i}`]: `use Array.${e}` }),
								Array.prototype[e].apply(t, [].slice.call(arguments, 1))
							);
						};
					}
				);
				let w = (function () {
					let t = Math.random().toString(36).substring(2, 9) + "-",
						i = 0;
					return function () {
						return "highcharts-" + (e ? "" : t) + i++;
					};
				})();
				return (
					r.jQuery &&
						(r.jQuery.fn.highcharts = function () {
							let e = [].slice.call(arguments);
							if (this[0])
								return e[0]
									? (new t[n(e[0]) ? e.shift() : "Chart"](this[0], e[0], e[1]),
									  this)
									: i[g(this[0], "data-highcharts-chart")];
						}),
					{
						addEvent: function (e, i, s, r = {}) {
							let o = ("function" == typeof e && e.prototype) || e;
							Object.hasOwnProperty.call(o, "hcEvents") || (o.hcEvents = {});
							let a = o.hcEvents;
							t.Point &&
								e instanceof t.Point &&
								e.series &&
								e.series.chart &&
								(e.series.chart.runTrackerClick = !0);
							let n = e.addEventListener;
							n &&
								n.call(
									e,
									i,
									s,
									!!t.supportsPassiveEvents && {
										passive:
											void 0 === r.passive
												? -1 !== i.indexOf("touch")
												: r.passive,
										capture: !1,
									}
								),
								a[i] || (a[i] = []);
							let h = {
								fn: s,
								order: "number" == typeof r.order ? r.order : 1 / 0,
							};
							return (
								a[i].push(h),
								a[i].sort((t, e) => t.order - e.order),
								function () {
									C(e, i, s);
								}
							);
						},
						arrayMax: function (t) {
							let e = t.length,
								i = t[0];
							for (; e--; ) t[e] > i && (i = t[e]);
							return i;
						},
						arrayMin: function (t) {
							let e = t.length,
								i = t[0];
							for (; e--; ) t[e] < i && (i = t[e]);
							return i;
						},
						attr: g,
						clamp: function (t, e, i) {
							return t > e ? (t < i ? t : i) : e;
						},
						clearTimeout: function (t) {
							u(t) && clearTimeout(t);
						},
						correctFloat: v,
						createElement: function (t, e, i, r, o) {
							let a = s.createElement(t);
							return (
								e && m(a, e),
								o && y(a, { padding: "0", border: "none", margin: "0" }),
								i && y(a, i),
								r && r.appendChild(a),
								a
							);
						},
						css: y,
						defined: u,
						destroyObjectProperties: function (t, e, i) {
							k(t, function (s, r) {
								s !== e && s?.destroy && s.destroy(),
									(s?.destroy || !i) && delete t[r];
							});
						},
						diffObjects: function (t, e, i, s) {
							let r = {};
							return (
								(function t(e, r, o, a) {
									let n = i ? r : e;
									k(e, function (i, d) {
										if (!a && s && s.indexOf(d) > -1 && r[d]) {
											(i = f(i)), (o[d] = []);
											for (let e = 0; e < Math.max(i.length, r[d].length); e++)
												r[d][e] &&
													(void 0 === i[e]
														? (o[d][e] = r[d][e])
														: ((o[d][e] = {}),
														  t(i[e], r[d][e], o[d][e], a + 1)));
										} else l(i, !0) && !i.nodeType ? ((o[d] = h(i) ? [] : {}), t(i, r[d] || {}, o[d], a + 1), 0 !== Object.keys(o[d]).length || ("colorAxis" === d && 0 === a) || delete o[d]) : (e[d] !== r[d] || (d in e && !(d in r))) && "__proto__" !== d && "constructor" !== d && (o[d] = n[d]);
									});
								})(t, e, r, 0),
								r
							);
						},
						discardElement: function (t) {
							t && t.parentElement && t.parentElement.removeChild(t);
						},
						erase: function (t, e) {
							let i = t.length;
							for (; i--; )
								if (t[i] === e) {
									t.splice(i, 1);
									break;
								}
						},
						error: o,
						extend: m,
						extendClass: function (t, e) {
							let i = function () {};
							return (i.prototype = new t()), m(i.prototype, e), i;
						},
						find: S,
						fireEvent: M,
						getClosestDistance: function (t, e) {
							let i, s, r, o;
							let a = !e;
							return (
								t.forEach((t) => {
									if (t.length > 1)
										for (o = s = t.length - 1; o > 0; o--)
											(r = t[o] - t[o - 1]) < 0 && !a
												? (e?.(), (e = void 0))
												: r && (void 0 === i || r < i) && (i = r);
								}),
								i
							);
						},
						getMagnitude: b,
						getNestedProperty: function (t, e) {
							let i = t.split(".");
							for (; i.length && u(e); ) {
								let t = i.shift();
								if (void 0 === t || "__proto__" === t) return;
								if ("this" === t) {
									let t;
									return l(e) && (t = e["@this"]), t ?? e;
								}
								let s = e[t];
								if (
									!u(s) ||
									"function" == typeof s ||
									"number" == typeof s.nodeType ||
									s === r
								)
									return;
								e = s;
							}
							return e;
						},
						getStyle: function t(e, i, s) {
							let o;
							if ("width" === i) {
								let i = Math.min(e.offsetWidth, e.scrollWidth),
									s =
										e.getBoundingClientRect && e.getBoundingClientRect().width;
								return (
									s < i && s >= i - 1 && (i = Math.floor(s)),
									Math.max(
										0,
										i -
											(t(e, "padding-left", !0) || 0) -
											(t(e, "padding-right", !0) || 0)
									)
								);
							}
							if ("height" === i)
								return Math.max(
									0,
									Math.min(e.offsetHeight, e.scrollHeight) -
										(t(e, "padding-top", !0) || 0) -
										(t(e, "padding-bottom", !0) || 0)
								);
							let n = r.getComputedStyle(e, void 0);
							return (
								n &&
									((o = n.getPropertyValue(i)),
									x(s, "opacity" !== i) && (o = a(o))),
								o
							);
						},
						inArray: function (t, e, i) {
							return (
								o(32, !1, void 0, {
									"Highcharts.inArray": "use Array.indexOf",
								}),
								e.indexOf(t, i)
							);
						},
						insertItem: function (t, e) {
							let i;
							let s = t.options.index,
								r = e.length;
							for (i = t.options.isInternal ? r : 0; i < r + 1; i++)
								if (
									!e[i] ||
									(p(s) && s < x(e[i].options.index, e[i]._i)) ||
									e[i].options.isInternal
								) {
									e.splice(i, 0, t);
									break;
								}
							return i;
						},
						isArray: h,
						isClass: c,
						isDOMElement: d,
						isFunction: function (t) {
							return "function" == typeof t;
						},
						isNumber: p,
						isObject: l,
						isString: n,
						keys: function (t) {
							return (
								o(32, !1, void 0, { "Highcharts.keys": "use Object.keys" }),
								Object.keys(t)
							);
						},
						merge: function () {
							let t,
								e = arguments,
								i = {},
								s = function (t, e) {
									return (
										"object" != typeof t && (t = {}),
										k(e, function (i, r) {
											"__proto__" !== r &&
												"constructor" !== r &&
												(!l(i, !0) || c(i) || d(i)
													? (t[r] = e[r])
													: (t[r] = s(t[r] || {}, i)));
										}),
										t
									);
								};
							!0 === e[0] &&
								((i = e[1]), (e = Array.prototype.slice.call(e, 2)));
							let r = e.length;
							for (t = 0; t < r; t++) i = s(i, e[t]);
							return i;
						},
						normalizeTickInterval: function (t, e, i, s, r) {
							let o,
								a = t;
							i = x(i, b(t));
							let n = t / i;
							for (
								!e &&
									((e = r
										? [1, 1.2, 1.5, 2, 2.5, 3, 4, 5, 6, 8, 10]
										: [1, 2, 2.5, 5, 10]),
									!1 === s &&
										(1 === i
											? (e = e.filter(function (t) {
													return t % 1 == 0;
											  }))
											: i <= 0.1 && (e = [1 / i]))),
									o = 0;
								o < e.length &&
								((a = e[o]),
								(!r || !(a * i >= t)) &&
									(r || !(n <= (e[o] + (e[o + 1] || e[o])) / 2)));
								o++
							);
							return v(a * i, -Math.round(Math.log(0.001) / Math.LN10));
						},
						objectEach: k,
						offset: function (t) {
							let e = s.documentElement,
								i =
									t.parentElement || t.parentNode
										? t.getBoundingClientRect()
										: { top: 0, left: 0, width: 0, height: 0 };
							return {
								top:
									i.top + (r.pageYOffset || e.scrollTop) - (e.clientTop || 0),
								left:
									i.left +
									(r.pageXOffset || e.scrollLeft) -
									(e.clientLeft || 0),
								width: i.width,
								height: i.height,
							};
						},
						pad: function (t, e, i) {
							return (
								Array((e || 2) + 1 - String(t).replace("-", "").length).join(
									i || "0"
								) + t
							);
						},
						pick: x,
						pInt: a,
						pushUnique: function (t, e) {
							return 0 > t.indexOf(e) && !!t.push(e);
						},
						relativeLength: function (t, e, i) {
							return /%$/.test(t)
								? (e * parseFloat(t)) / 100 + (i || 0)
								: parseFloat(t);
						},
						removeEvent: C,
						replaceNested: function (t, ...e) {
							let i, s;
							do for (s of ((i = t), e)) t = t.replace(s[0], s[1]);
							while (t !== i);
							return t;
						},
						splat: f,
						stableSort: function (t, e) {
							let i, s;
							let r = t.length;
							for (s = 0; s < r; s++) t[s].safeI = s;
							for (
								t.sort(function (t, s) {
									return 0 === (i = e(t, s)) ? t.safeI - s.safeI : i;
								}),
									s = 0;
								s < r;
								s++
							)
								delete t[s].safeI;
						},
						syncTimeout: function (t, e, i) {
							return e > 0 ? setTimeout(t, e, i) : (t.call(0, i), -1);
						},
						timeUnits: {
							millisecond: 1,
							second: 1e3,
							minute: 6e4,
							hour: 36e5,
							day: 864e5,
							week: 6048e5,
							month: 24192e5,
							year: 314496e5,
						},
						uniqueKey: w,
						useSerialIds: function (t) {
							return (e = x(t, e));
						},
						wrap: function (t, e, i) {
							let s = t[e];
							t[e] = function () {
								let t = arguments,
									e = this;
								return i.apply(
									this,
									[
										function () {
											return s.apply(e, arguments.length ? arguments : t);
										},
									].concat([].slice.call(arguments))
								);
							};
						},
					}
				);
			}),
			i(e, "Core/Chart/ChartDefaults.js", [], function () {
				return {
					alignThresholds: !1,
					panning: { enabled: !1, type: "x" },
					styledMode: !1,
					borderRadius: 0,
					colorCount: 10,
					allowMutatingData: !0,
					ignoreHiddenSeries: !0,
					spacing: [10, 10, 15, 10],
					resetZoomButton: { theme: {}, position: {} },
					reflow: !0,
					type: "line",
					zooming: {
						singleTouch: !1,
						resetButton: {
							theme: { zIndex: 6 },
							position: { align: "right", x: -10, y: 10 },
						},
					},
					width: null,
					height: null,
					borderColor: "#334eff",
					backgroundColor: "#ffffff",
					plotBorderColor: "#cccccc",
				};
			}),
			i(e, "Core/Color/Palettes.js", [], function () {
				return {
					colors: [
						"#2caffe",
						"#544fc5",
						"#00e272",
						"#fe6a35",
						"#6b8abc",
						"#d568fb",
						"#2ee0ca",
						"#fa4b42",
						"#feb56a",
						"#91e8e1",
					],
				};
			}),
			i(
				e,
				"Core/Time.js",
				[e["Core/Globals.js"], e["Core/Utilities.js"]],
				function (t, e) {
					let { win: i } = t,
						{
							defined: s,
							error: r,
							extend: o,
							isNumber: a,
							isObject: n,
							merge: h,
							objectEach: l,
							pad: d,
							pick: c,
							splat: p,
							timeUnits: u,
						} = e,
						g =
							t.isSafari &&
							i.Intl &&
							i.Intl.DateTimeFormat.prototype.formatRange,
						f =
							t.isSafari &&
							i.Intl &&
							!i.Intl.DateTimeFormat.prototype.formatRange;
					class m {
						constructor(t) {
							(this.options = {}),
								(this.useUTC = !1),
								(this.variableTimezone = !1),
								(this.Date = i.Date),
								(this.getTimezoneOffset = this.timezoneOffsetFunction()),
								this.update(t);
						}
						get(t, e) {
							if (this.variableTimezone || this.timezoneOffset) {
								let i = e.getTime(),
									s = i - this.getTimezoneOffset(e);
								e.setTime(s);
								let r = e["getUTC" + t]();
								return e.setTime(i), r;
							}
							return this.useUTC ? e["getUTC" + t]() : e["get" + t]();
						}
						set(t, e, i) {
							if (this.variableTimezone || this.timezoneOffset) {
								if (
									"Milliseconds" === t ||
									"Seconds" === t ||
									("Minutes" === t && this.getTimezoneOffset(e) % 36e5 == 0)
								)
									return e["setUTC" + t](i);
								let s = this.getTimezoneOffset(e),
									r = e.getTime() - s;
								e.setTime(r), e["setUTC" + t](i);
								let o = this.getTimezoneOffset(e);
								return (r = e.getTime() + o), e.setTime(r);
							}
							return this.useUTC || (g && "FullYear" === t)
								? e["setUTC" + t](i)
								: e["set" + t](i);
						}
						update(t = {}) {
							let e = c(t.useUTC, !0);
							(this.options = t = h(!0, this.options, t)),
								(this.Date = t.Date || i.Date || Date),
								(this.useUTC = e),
								(this.timezoneOffset = (e && t.timezoneOffset) || void 0),
								(this.getTimezoneOffset = this.timezoneOffsetFunction()),
								(this.variableTimezone =
									e && !!(t.getTimezoneOffset || t.timezone));
						}
						makeTime(t, e, i, s, r, o) {
							let a, n, h;
							return (
								this.useUTC
									? ((a = this.Date.UTC.apply(0, arguments)),
									  (n = this.getTimezoneOffset(a)),
									  (a += n),
									  n !== (h = this.getTimezoneOffset(a))
											? (a += h - n)
											: n - 36e5 !== this.getTimezoneOffset(a - 36e5) ||
											  f ||
											  (a -= 36e5))
									: (a = new this.Date(
											t,
											e,
											c(i, 1),
											c(s, 0),
											c(r, 0),
											c(o, 0)
									  ).getTime()),
								a
							);
						}
						timezoneOffsetFunction() {
							let t = this,
								e = this.options,
								i = e.getTimezoneOffset;
							return this.useUTC
								? e.timezone
									? (t) => {
											try {
												let i = `shortOffset,${e.timezone || ""}`,
													[s, r, o, n, h = 0] = (m.formatCache[i] =
														m.formatCache[i] ||
														Intl.DateTimeFormat("en", {
															timeZone: e.timezone,
															timeZoneName: "shortOffset",
														}))
														.format(t)
														.split(/(GMT|:)/)
														.map(Number),
													l = -(36e5 * (o + h / 60));
												if (a(l)) return l;
											} catch (t) {
												r(34);
											}
											return 0;
									  }
									: this.useUTC && i
									? (t) => 6e4 * i(t.valueOf())
									: () => 6e4 * (t.timezoneOffset || 0)
								: (t) => 6e4 * new Date(t.toString()).getTimezoneOffset();
						}
						dateFormat(e, i, r) {
							if (!s(i) || isNaN(i))
								return (
									(t.defaultOptions.lang &&
										t.defaultOptions.lang.invalidDate) ||
									""
								);
							e = c(e, "%Y-%m-%d %H:%M:%S");
							let a = this,
								n = new this.Date(i),
								h = this.get("Hours", n),
								p = this.get("Day", n),
								u = this.get("Date", n),
								g = this.get("Month", n),
								f = this.get("FullYear", n),
								m = t.defaultOptions.lang,
								x = m && m.weekdays,
								y = m && m.shortWeekdays;
							return (
								l(
									o(
										{
											a: y ? y[p] : x[p].substr(0, 3),
											A: x[p],
											d: d(u),
											e: d(u, 2, " "),
											w: p,
											b: m.shortMonths[g],
											B: m.months[g],
											m: d(g + 1),
											o: g + 1,
											y: f.toString().substr(2, 2),
											Y: f,
											H: d(h),
											k: h,
											I: d(h % 12 || 12),
											l: h % 12 || 12,
											M: d(this.get("Minutes", n)),
											p: h < 12 ? "AM" : "PM",
											P: h < 12 ? "am" : "pm",
											S: d(this.get("Seconds", n)),
											L: d(Math.floor(i % 1e3), 3),
										},
										t.dateFormats
									),
									function (t, s) {
										for (; -1 !== e.indexOf("%" + s); )
											e = e.replace(
												"%" + s,
												"function" == typeof t ? t.call(a, i) : t
											);
									}
								),
								r ? e.substr(0, 1).toUpperCase() + e.substr(1) : e
							);
						}
						resolveDTLFormat(t) {
							return n(t, !0)
								? t
								: { main: (t = p(t))[0], from: t[1], to: t[2] };
						}
						getTimeTicks(t, e, i, r) {
							let a, n, h, l;
							let d = this,
								p = d.Date,
								g = [],
								f = {},
								m = new p(e),
								x = t.unitRange,
								y = t.count || 1;
							if (((r = c(r, 1)), s(e))) {
								d.set(
									"Milliseconds",
									m,
									x >= u.second
										? 0
										: y * Math.floor(d.get("Milliseconds", m) / y)
								),
									x >= u.second &&
										d.set(
											"Seconds",
											m,
											x >= u.minute
												? 0
												: y * Math.floor(d.get("Seconds", m) / y)
										),
									x >= u.minute &&
										d.set(
											"Minutes",
											m,
											x >= u.hour ? 0 : y * Math.floor(d.get("Minutes", m) / y)
										),
									x >= u.hour &&
										d.set(
											"Hours",
											m,
											x >= u.day ? 0 : y * Math.floor(d.get("Hours", m) / y)
										),
									x >= u.day &&
										d.set(
											"Date",
											m,
											x >= u.month
												? 1
												: Math.max(1, y * Math.floor(d.get("Date", m) / y))
										),
									x >= u.month &&
										(d.set(
											"Month",
											m,
											x >= u.year ? 0 : y * Math.floor(d.get("Month", m) / y)
										),
										(n = d.get("FullYear", m))),
									x >= u.year && ((n -= n % y), d.set("FullYear", m, n)),
									x === u.week &&
										((l = d.get("Day", m)),
										d.set(
											"Date",
											m,
											d.get("Date", m) - l + r + (l < r ? -7 : 0)
										)),
									(n = d.get("FullYear", m));
								let t = d.get("Month", m),
									o = d.get("Date", m),
									c = d.get("Hours", m);
								(e = m.getTime()),
									(d.variableTimezone || !d.useUTC) &&
										s(i) &&
										(h =
											i - e > 4 * u.month ||
											d.getTimezoneOffset(e) !== d.getTimezoneOffset(i));
								let p = m.getTime();
								for (a = 1; p < i; )
									g.push(p),
										x === u.year
											? (p = d.makeTime(n + a * y, 0))
											: x === u.month
											? (p = d.makeTime(n, t + a * y))
											: h && (x === u.day || x === u.week)
											? (p = d.makeTime(
													n,
													t,
													o + a * y * (x === u.day ? 1 : 7)
											  ))
											: h && x === u.hour && y > 1
											? (p = d.makeTime(n, t, o, c + a * y))
											: (p += x * y),
										a++;
								g.push(p),
									x <= u.hour &&
										g.length < 1e4 &&
										g.forEach(function (t) {
											t % 18e5 == 0 &&
												"000000000" === d.dateFormat("%H%M%S%L", t) &&
												(f[t] = "day");
										});
							}
							return (g.info = o(t, { higherRanks: f, totalRange: x * y })), g;
						}
						getDateFormat(t, e, i, s) {
							let r = this.dateFormat("%m-%d %H:%M:%S.%L", e),
								o = "01-01 00:00:00.000",
								a = { millisecond: 15, second: 12, minute: 9, hour: 6, day: 3 },
								n = "millisecond",
								h = n;
							for (n in u) {
								if (
									t === u.week &&
									+this.dateFormat("%w", e) === i &&
									r.substr(6) === o.substr(6)
								) {
									n = "week";
									break;
								}
								if (u[n] > t) {
									n = h;
									break;
								}
								if (a[n] && r.substr(a[n]) !== o.substr(a[n])) break;
								"week" !== n && (h = n);
							}
							return this.resolveDTLFormat(s[n]).main;
						}
					}
					return (m.formatCache = {}), m;
				}
			),
			i(
				e,
				"Core/Defaults.js",
				[
					e["Core/Chart/ChartDefaults.js"],
					e["Core/Globals.js"],
					e["Core/Color/Palettes.js"],
					e["Core/Time.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r) {
					let { isTouchDevice: o, svg: a } = e,
						{ merge: n } = r,
						h = {
							colors: i.colors,
							symbols: [
								"circle",
								"diamond",
								"square",
								"triangle",
								"triangle-down",
							],
							lang: {
								loading: "Loading...",
								months: [
									"January",
									"February",
									"March",
									"April",
									"May",
									"June",
									"July",
									"August",
									"September",
									"October",
									"November",
									"December",
								],
								shortMonths: [
									"Jan",
									"Feb",
									"Mar",
									"Apr",
									"May",
									"Jun",
									"Jul",
									"Aug",
									"Sep",
									"Oct",
									"Nov",
									"Dec",
								],
								weekdays: [
									"Sunday",
									"Monday",
									"Tuesday",
									"Wednesday",
									"Thursday",
									"Friday",
									"Saturday",
								],
								decimalPoint: ".",
								numericSymbols: ["k", "M", "G", "T", "P", "E"],
								resetZoom: "Reset zoom",
								resetZoomTitle: "Reset zoom level 1:1",
								thousandsSep: " ",
							},
							global: {},
							time: {
								Date: void 0,
								getTimezoneOffset: void 0,
								timezone: void 0,
								timezoneOffset: 0,
								useUTC: !0,
							},
							chart: t,
							title: {
								style: { color: "#333333", fontWeight: "bold" },
								text: "Chart title",
								align: "center",
								margin: 15,
								widthAdjust: -44,
							},
							subtitle: {
								style: { color: "#666666", fontSize: "0.8em" },
								text: "",
								align: "center",
								widthAdjust: -44,
							},
							caption: {
								margin: 15,
								style: { color: "#666666", fontSize: "0.8em" },
								text: "",
								align: "left",
								verticalAlign: "bottom",
							},
							plotOptions: {},
							legend: {
								enabled: !0,
								align: "center",
								alignColumns: !0,
								className: "highcharts-no-tooltip",
								layout: "horizontal",
								itemMarginBottom: 2,
								itemMarginTop: 2,
								labelFormatter: function () {
									return this.name;
								},
								borderColor: "#999999",
								borderRadius: 0,
								navigation: {
									style: { fontSize: "0.8em" },
									activeColor: "#0022ff",
									inactiveColor: "#cccccc",
								},
								itemStyle: {
									color: "#333333",
									cursor: "pointer",
									fontSize: "0.8em",
									textDecoration: "none",
									textOverflow: "ellipsis",
								},
								itemHoverStyle: { color: "#000000" },
								itemHiddenStyle: {
									color: "#666666",
									textDecoration: "line-through",
								},
								shadow: !1,
								itemCheckboxStyle: {
									position: "absolute",
									width: "13px",
									height: "13px",
								},
								squareSymbol: !0,
								symbolPadding: 5,
								verticalAlign: "bottom",
								x: 0,
								y: 0,
								title: { style: { fontSize: "0.8em", fontWeight: "bold" } },
							},
							loading: {
								labelStyle: {
									fontWeight: "bold",
									position: "relative",
									top: "45%",
								},
								style: {
									position: "absolute",
									backgroundColor: "#ffffff",
									opacity: 0.5,
									textAlign: "center",
								},
							},
							tooltip: {
								enabled: !0,
								animation: a,
								borderRadius: 3,
								dateTimeLabelFormats: {
									millisecond: "%A, %e %b, %H:%M:%S.%L",
									second: "%A, %e %b, %H:%M:%S",
									minute: "%A, %e %b, %H:%M",
									hour: "%A, %e %b, %H:%M",
									day: "%A, %e %b %Y",
									week: "Week from %A, %e %b %Y",
									month: "%B %Y",
									year: "%Y",
								},
								footerFormat: "",
								headerShape: "callout",
								hideDelay: 500,
								padding: 8,
								shape: "callout",
								shared: !1,
								snap: o ? 25 : 10,
								headerFormat:
									'<span style="font-size: 0.8em">{point.key}</span><br/>',
								pointFormat:
									'<span style="color:{point.color}">●</span> {series.name}: <b>{point.y}</b><br/>',
								backgroundColor: "#ffffff",
								borderWidth: void 0,
								shadow: !0,
								stickOnContact: !1,
								style: {
									color: "#333333",
									cursor: "default",
									fontSize: "0.8em",
								},
								useHTML: !1,
							},
							credits: {
								enabled: !0,
								href: "https://www.highcharts.com?credits",
								position: {
									align: "right",
									x: -10,
									verticalAlign: "bottom",
									y: -5,
								},
								style: {
									cursor: "pointer",
									color: "#999999",
									fontSize: "0.6em",
								},
								text: "Highcharts.com",
							},
						};
					h.chart.styledMode = !1;
					let l = new s(h.time);
					return {
						defaultOptions: h,
						defaultTime: l,
						getOptions: function () {
							return h;
						},
						setOptions: function (t) {
							return (
								n(!0, h, t),
								(t.time || t.global) &&
									(e.time
										? e.time.update(n(h.global, h.time, t.global, t.time))
										: (e.time = l)),
								h
							);
						},
					};
				}
			),
			i(
				e,
				"Core/Color/Color.js",
				[e["Core/Globals.js"], e["Core/Utilities.js"]],
				function (t, e) {
					let { isNumber: i, merge: s, pInt: r } = e;
					class o {
						static parse(t) {
							return t ? new o(t) : o.None;
						}
						constructor(e) {
							let i, s, r, a;
							(this.rgba = [NaN, NaN, NaN, NaN]), (this.input = e);
							let n = t.Color;
							if (n && n !== o) return new n(e);
							if ("object" == typeof e && void 0 !== e.stops)
								this.stops = e.stops.map((t) => new o(t[1]));
							else if ("string" == typeof e) {
								if (
									((this.input = e = o.names[e.toLowerCase()] || e),
									"#" === e.charAt(0))
								) {
									let t = e.length,
										i = parseInt(e.substr(1), 16);
									7 === t
										? (s = [(16711680 & i) >> 16, (65280 & i) >> 8, 255 & i, 1])
										: 4 === t &&
										  (s = [
												((3840 & i) >> 4) | ((3840 & i) >> 8),
												((240 & i) >> 4) | (240 & i),
												((15 & i) << 4) | (15 & i),
												1,
										  ]);
								}
								if (!s)
									for (r = o.parsers.length; r-- && !s; )
										(i = (a = o.parsers[r]).regex.exec(e)) && (s = a.parse(i));
							}
							s && (this.rgba = s);
						}
						get(t) {
							let e = this.input,
								r = this.rgba;
							if ("object" == typeof e && void 0 !== this.stops) {
								let i = s(e);
								return (
									(i.stops = [].slice.call(i.stops)),
									this.stops.forEach((e, s) => {
										i.stops[s] = [i.stops[s][0], e.get(t)];
									}),
									i
								);
							}
							return r && i(r[0])
								? "rgb" !== t && (t || 1 !== r[3])
									? "a" === t
										? `${r[3]}`
										: "rgba(" + r.join(",") + ")"
									: "rgb(" + r[0] + "," + r[1] + "," + r[2] + ")"
								: e;
						}
						brighten(t) {
							let e = this.rgba;
							if (this.stops)
								this.stops.forEach(function (e) {
									e.brighten(t);
								});
							else if (i(t) && 0 !== t)
								for (let i = 0; i < 3; i++)
									(e[i] += r(255 * t)),
										e[i] < 0 && (e[i] = 0),
										e[i] > 255 && (e[i] = 255);
							return this;
						}
						setOpacity(t) {
							return (this.rgba[3] = t), this;
						}
						tweenTo(t, e) {
							let s = this.rgba,
								r = t.rgba;
							if (!i(s[0]) || !i(r[0])) return t.input || "none";
							let o = 1 !== r[3] || 1 !== s[3];
							return (
								(o ? "rgba(" : "rgb(") +
								Math.round(r[0] + (s[0] - r[0]) * (1 - e)) +
								"," +
								Math.round(r[1] + (s[1] - r[1]) * (1 - e)) +
								"," +
								Math.round(r[2] + (s[2] - r[2]) * (1 - e)) +
								(o ? "," + (r[3] + (s[3] - r[3]) * (1 - e)) : "") +
								")"
							);
						}
					}
					return (
						(o.names = { white: "#ffffff", black: "#000000" }),
						(o.parsers = [
							{
								regex:
									/rgba\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]?(?:\.[0-9]+)?)\s*\)/,
								parse: function (t) {
									return [r(t[1]), r(t[2]), r(t[3]), parseFloat(t[4], 10)];
								},
							},
							{
								regex:
									/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/,
								parse: function (t) {
									return [r(t[1]), r(t[2]), r(t[3]), 1];
								},
							},
						]),
						(o.None = new o("")),
						o
					);
				}
			),
			i(
				e,
				"Core/Animation/Fx.js",
				[
					e["Core/Color/Color.js"],
					e["Core/Globals.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					let { parse: s } = t,
						{ win: r } = e,
						{ isNumber: o, objectEach: a } = i;
					class n {
						constructor(t, e, i) {
							(this.pos = NaN),
								(this.options = e),
								(this.elem = t),
								(this.prop = i);
						}
						dSetter() {
							let t = this.paths,
								e = t && t[0],
								i = t && t[1],
								s = this.now || 0,
								r = [];
							if (1 !== s && e && i) {
								if (e.length === i.length && s < 1)
									for (let t = 0; t < i.length; t++) {
										let a = e[t],
											n = i[t],
											h = [];
										for (let t = 0; t < n.length; t++) {
											let e = a[t],
												i = n[t];
											o(e) && o(i) && !("A" === n[0] && (4 === t || 5 === t))
												? (h[t] = e + s * (i - e))
												: (h[t] = i);
										}
										r.push(h);
									}
								else r = i;
							} else r = this.toD || [];
							this.elem.attr("d", r, void 0, !0);
						}
						update() {
							let t = this.elem,
								e = this.prop,
								i = this.now,
								s = this.options.step;
							this[e + "Setter"]
								? this[e + "Setter"]()
								: t.attr
								? t.element && t.attr(e, i, null, !0)
								: (t.style[e] = i + this.unit),
								s && s.call(t, i, this);
						}
						run(t, e, i) {
							let s = this,
								o = s.options,
								a = function (t) {
									return !a.stopped && s.step(t);
								},
								h =
									r.requestAnimationFrame ||
									function (t) {
										setTimeout(t, 13);
									},
								l = function () {
									for (let t = 0; t < n.timers.length; t++)
										n.timers[t]() || n.timers.splice(t--, 1);
									n.timers.length && h(l);
								};
							t !== e || this.elem["forceAnimate:" + this.prop]
								? ((this.startTime = +new Date()),
								  (this.start = t),
								  (this.end = e),
								  (this.unit = i),
								  (this.now = this.start),
								  (this.pos = 0),
								  (a.elem = this.elem),
								  (a.prop = this.prop),
								  a() && 1 === n.timers.push(a) && h(l))
								: (delete o.curAnim[this.prop],
								  o.complete &&
										0 === Object.keys(o.curAnim).length &&
										o.complete.call(this.elem));
						}
						step(t) {
							let e, i;
							let s = +new Date(),
								r = this.options,
								o = this.elem,
								n = r.complete,
								h = r.duration,
								l = r.curAnim;
							return (
								o.attr && !o.element
									? (e = !1)
									: t || s >= h + this.startTime
									? ((this.now = this.end),
									  (this.pos = 1),
									  this.update(),
									  (l[this.prop] = !0),
									  (i = !0),
									  a(l, function (t) {
											!0 !== t && (i = !1);
									  }),
									  i && n && n.call(o),
									  (e = !1))
									: ((this.pos = r.easing((s - this.startTime) / h)),
									  (this.now =
											this.start + (this.end - this.start) * this.pos),
									  this.update(),
									  (e = !0)),
								e
							);
						}
						initPath(t, e, i) {
							let s = t.startX,
								r = t.endX,
								a = i.slice(),
								n = t.isArea,
								h = n ? 2 : 1,
								l,
								d,
								c,
								p,
								u = e && e.slice();
							if (!u) return [a, a];
							function g(t, e) {
								for (; t.length < d; ) {
									let i = t[0],
										s = e[d - t.length];
									if (
										(s &&
											"M" === i[0] &&
											("C" === s[0]
												? (t[0] = ["C", i[1], i[2], i[1], i[2], i[1], i[2]])
												: (t[0] = ["L", i[1], i[2]])),
										t.unshift(i),
										n)
									) {
										let e = t.pop();
										t.push(t[t.length - 1], e);
									}
								}
							}
							function f(t) {
								for (; t.length < d; ) {
									let e = t[Math.floor(t.length / h) - 1].slice();
									if (("C" === e[0] && ((e[1] = e[5]), (e[2] = e[6])), n)) {
										let i = t[Math.floor(t.length / h)].slice();
										t.splice(t.length / 2, 0, e, i);
									} else t.push(e);
								}
							}
							if (s && r && r.length) {
								for (c = 0; c < s.length; c++) {
									if (s[c] === r[0]) {
										l = c;
										break;
									}
									if (s[0] === r[r.length - s.length + c]) {
										(l = c), (p = !0);
										break;
									}
									if (s[s.length - 1] === r[r.length - s.length + c]) {
										l = s.length - c;
										break;
									}
								}
								void 0 === l && (u = []);
							}
							return (
								u.length &&
									o(l) &&
									((d = a.length + l * h),
									p ? (g(u, a), f(a)) : (g(a, u), f(u))),
								[u, a]
							);
						}
						fillSetter() {
							n.prototype.strokeSetter.apply(this, arguments);
						}
						strokeSetter() {
							this.elem.attr(
								this.prop,
								s(this.start).tweenTo(s(this.end), this.pos),
								void 0,
								!0
							);
						}
					}
					return (n.timers = []), n;
				}
			),
			i(
				e,
				"Core/Animation/AnimationUtilities.js",
				[e["Core/Animation/Fx.js"], e["Core/Utilities.js"]],
				function (t, e) {
					let {
						defined: i,
						getStyle: s,
						isArray: r,
						isNumber: o,
						isObject: a,
						merge: n,
						objectEach: h,
						pick: l,
					} = e;
					function d(t) {
						return a(t)
							? n({ duration: 500, defer: 0 }, t)
							: { duration: t ? 500 : 0, defer: 0 };
					}
					function c(e, i) {
						let s = t.timers.length;
						for (; s--; )
							t.timers[s].elem !== e ||
								(i && i !== t.timers[s].prop) ||
								(t.timers[s].stopped = !0);
					}
					return {
						animate: function (e, i, l) {
							let d,
								p = "",
								u,
								g,
								f;
							a(l) ||
								((f = arguments),
								(l = { duration: f[2], easing: f[3], complete: f[4] })),
								o(l.duration) || (l.duration = 400),
								(l.easing =
									"function" == typeof l.easing
										? l.easing
										: Math[l.easing] || Math.easeInOutSine),
								(l.curAnim = n(i)),
								h(i, function (o, a) {
									c(e, a),
										(g = new t(e, l, a)),
										(u = void 0),
										"d" === a && r(i.d)
											? ((g.paths = g.initPath(e, e.pathArray, i.d)),
											  (g.toD = i.d),
											  (d = 0),
											  (u = 1))
											: e.attr
											? (d = e.attr(a))
											: ((d = parseFloat(s(e, a)) || 0),
											  "opacity" !== a && (p = "px")),
										u || (u = o),
										"string" == typeof u &&
											u.match("px") &&
											(u = u.replace(/px/g, "")),
										g.run(d, u, p);
								});
						},
						animObject: d,
						getDeferredAnimation: function (t, e, s) {
							let r = d(e),
								o = s ? [s] : t.series,
								n = 0,
								h = 0;
							return (
								o.forEach((t) => {
									let s = d(t.options.animation);
									(n =
										a(e) && i(e.defer)
											? r.defer
											: Math.max(n, s.duration + s.defer)),
										(h = Math.min(r.duration, s.duration));
								}),
								t.renderer.forExport && (n = 0),
								{ defer: Math.max(0, n - h), duration: Math.min(n, h) }
							);
						},
						setAnimation: function (t, e) {
							e.renderer.globalAnimation = l(t, e.options.chart.animation, !0);
						},
						stop: c,
					};
				}
			),
			i(
				e,
				"Core/Renderer/HTML/AST.js",
				[e["Core/Globals.js"], e["Core/Utilities.js"]],
				function (t, e) {
					let { SVG_NS: i, win: s } = t,
						{
							attr: r,
							createElement: o,
							css: a,
							error: n,
							isFunction: h,
							isString: l,
							objectEach: d,
							splat: c,
						} = e,
						{ trustedTypes: p } = s,
						u =
							p &&
							h(p.createPolicy) &&
							p.createPolicy("highcharts", { createHTML: (t) => t }),
						g = u ? u.createHTML("") : "",
						f = (function () {
							try {
								return !!new DOMParser().parseFromString(g, "text/html");
							} catch (t) {
								return !1;
							}
						})();
					class m {
						static filterUserAttributes(t) {
							return (
								d(t, (e, i) => {
									let s = !0;
									-1 === m.allowedAttributes.indexOf(i) && (s = !1),
										-1 !==
											["background", "dynsrc", "href", "lowsrc", "src"].indexOf(
												i
											) &&
											(s =
												l(e) &&
												m.allowedReferences.some((t) => 0 === e.indexOf(t))),
										s ||
											(n(33, !1, void 0, {
												"Invalid attribute in config": `${i}`,
											}),
											delete t[i]),
										l(e) && t[i] && (t[i] = e.replace(/</g, "&lt;"));
								}),
								t
							);
						}
						static parseStyle(t) {
							return t.split(";").reduce((t, e) => {
								let i = e.split(":").map((t) => t.trim()),
									s = i.shift();
								return (
									s &&
										i.length &&
										(t[s.replace(/-([a-z])/g, (t) => t[1].toUpperCase())] =
											i.join(":")),
									t
								);
							}, {});
						}
						static setElementHTML(t, e) {
							(t.innerHTML = m.emptyHTML), e && new m(e).addToDOM(t);
						}
						constructor(t) {
							this.nodes = "string" == typeof t ? this.parseMarkup(t) : t;
						}
						addToDOM(e) {
							return (function e(s, o) {
								let h;
								return (
									c(s).forEach(function (s) {
										let l;
										let c = s.tagName,
											p = s.textContent
												? t.doc.createTextNode(s.textContent)
												: void 0,
											u = m.bypassHTMLFiltering;
										if (c) {
											if ("#text" === c) l = p;
											else if (-1 !== m.allowedTags.indexOf(c) || u) {
												let n = "svg" === c ? i : o.namespaceURI || i,
													h = t.doc.createElementNS(n, c),
													g = s.attributes || {};
												d(s, function (t, e) {
													"tagName" !== e &&
														"attributes" !== e &&
														"children" !== e &&
														"style" !== e &&
														"textContent" !== e &&
														(g[e] = t);
												}),
													r(h, u ? g : m.filterUserAttributes(g)),
													s.style && a(h, s.style),
													p && h.appendChild(p),
													e(s.children || [], h),
													(l = h);
											} else
												n(33, !1, void 0, { "Invalid tagName in config": c });
										}
										l && o.appendChild(l), (h = l);
									}),
									h
								);
							})(this.nodes, e);
						}
						parseMarkup(t) {
							let e;
							let i = [];
							if (
								((t = t.trim().replace(/ style=(["'])/g, " data-style=$1")), f)
							)
								e = new DOMParser().parseFromString(
									u ? u.createHTML(t) : t,
									"text/html"
								);
							else {
								let i = o("div");
								(i.innerHTML = t), (e = { body: i });
							}
							let s = (t, e) => {
								let i = t.nodeName.toLowerCase(),
									r = { tagName: i };
								"#text" === i && (r.textContent = t.textContent || "");
								let o = t.attributes;
								if (o) {
									let t = {};
									[].forEach.call(o, (e) => {
										"data-style" === e.name
											? (r.style = m.parseStyle(e.value))
											: (t[e.name] = e.value);
									}),
										(r.attributes = t);
								}
								if (t.childNodes.length) {
									let e = [];
									[].forEach.call(t.childNodes, (t) => {
										s(t, e);
									}),
										e.length && (r.children = e);
								}
								e.push(r);
							};
							return [].forEach.call(e.body.childNodes, (t) => s(t, i)), i;
						}
					}
					return (
						(m.allowedAttributes = [
							"alt",
							"aria-controls",
							"aria-describedby",
							"aria-expanded",
							"aria-haspopup",
							"aria-hidden",
							"aria-label",
							"aria-labelledby",
							"aria-live",
							"aria-pressed",
							"aria-readonly",
							"aria-roledescription",
							"aria-selected",
							"class",
							"clip-path",
							"color",
							"colspan",
							"cx",
							"cy",
							"d",
							"dx",
							"dy",
							"disabled",
							"fill",
							"filterUnits",
							"flood-color",
							"flood-opacity",
							"height",
							"href",
							"id",
							"in",
							"markerHeight",
							"markerWidth",
							"offset",
							"opacity",
							"orient",
							"padding",
							"paddingLeft",
							"paddingRight",
							"patternUnits",
							"r",
							"refX",
							"refY",
							"role",
							"scope",
							"slope",
							"src",
							"startOffset",
							"stdDeviation",
							"stroke",
							"stroke-linecap",
							"stroke-width",
							"style",
							"tableValues",
							"result",
							"rowspan",
							"summary",
							"target",
							"tabindex",
							"text-align",
							"text-anchor",
							"textAnchor",
							"textLength",
							"title",
							"type",
							"valign",
							"width",
							"x",
							"x1",
							"x2",
							"xlink:href",
							"y",
							"y1",
							"y2",
							"zIndex",
						]),
						(m.allowedReferences = [
							"https://",
							"http://",
							"mailto:",
							"/",
							"../",
							"./",
							"#",
						]),
						(m.allowedTags = [
							"a",
							"abbr",
							"b",
							"br",
							"button",
							"caption",
							"circle",
							"clipPath",
							"code",
							"dd",
							"defs",
							"div",
							"dl",
							"dt",
							"em",
							"feComponentTransfer",
							"feDropShadow",
							"feFuncA",
							"feFuncB",
							"feFuncG",
							"feFuncR",
							"feGaussianBlur",
							"feOffset",
							"feMerge",
							"feMergeNode",
							"filter",
							"h1",
							"h2",
							"h3",
							"h4",
							"h5",
							"h6",
							"hr",
							"i",
							"img",
							"li",
							"linearGradient",
							"marker",
							"ol",
							"p",
							"path",
							"pattern",
							"pre",
							"rect",
							"small",
							"span",
							"stop",
							"strong",
							"style",
							"sub",
							"sup",
							"svg",
							"table",
							"text",
							"textPath",
							"thead",
							"title",
							"tbody",
							"tspan",
							"td",
							"th",
							"tr",
							"u",
							"ul",
							"#text",
						]),
						(m.emptyHTML = g),
						(m.bypassHTMLFiltering = !1),
						m
					);
				}
			),
			i(
				e,
				"Core/Templating.js",
				[e["Core/Defaults.js"], e["Core/Utilities.js"]],
				function (t, e) {
					let { defaultOptions: i, defaultTime: s } = t,
						{
							extend: r,
							getNestedProperty: o,
							isArray: a,
							isNumber: n,
							isObject: h,
							pick: l,
							pInt: d,
						} = e,
						c = {
							add: (t, e) => t + e,
							divide: (t, e) => (0 !== e ? t / e : ""),
							eq: (t, e) => t == e,
							each: function (t) {
								let e = arguments[arguments.length - 1];
								return (
									!!a(t) &&
									t
										.map((i, s) =>
											p(
												e.body,
												r(h(i) ? i : { "@this": i }, {
													"@index": s,
													"@first": 0 === s,
													"@last": s === t.length - 1,
												})
											)
										)
										.join("")
								);
							},
							ge: (t, e) => t >= e,
							gt: (t, e) => t > e,
							if: (t) => !!t,
							le: (t, e) => t <= e,
							lt: (t, e) => t < e,
							multiply: (t, e) => t * e,
							ne: (t, e) => t != e,
							subtract: (t, e) => t - e,
							unless: (t) => !t,
						};
					function p(t = "", e, r) {
						let a = /\{([a-zA-Z0-9\:\.\,;\-\/<>%_@"'= #\(\)]+)\}/g,
							n = /\(([a-zA-Z0-9\:\.\,;\-\/<>%_@"'= ]+)\)/g,
							h = [],
							d = /f$/,
							g = /\.([0-9])/,
							f = i.lang,
							m = (r && r.time) || s,
							x = (r && r.numberFormatter) || u,
							y = (t = "") => {
								let i;
								return (
									"true" === t ||
									("false" !== t &&
										((i = Number(t)).toString() === t ? i : o(t, e)))
								);
							},
							b,
							v,
							S = 0,
							k;
						for (; null !== (b = a.exec(t)); ) {
							let i = n.exec(b[1]);
							i && ((b = i), (k = !0)),
								(v && v.isBlock) ||
									(v = {
										ctx: e,
										expression: b[1],
										find: b[0],
										isBlock: "#" === b[1].charAt(0),
										start: b.index,
										startInner: b.index + b[0].length,
										length: b[0].length,
									});
							let s = b[1].split(" ")[0].replace("#", "");
							c[s] && (v.isBlock && s === v.fn && S++, v.fn || (v.fn = s));
							let r = "else" === b[1];
							if (v.isBlock && v.fn && (b[1] === `/${v.fn}` || r)) {
								if (S) !r && S--;
								else {
									let e = v.startInner,
										i = t.substr(e, b.index - e);
									void 0 === v.body
										? ((v.body = i), (v.startInner = b.index + b[0].length))
										: (v.elseBody = i),
										(v.find += i + b[0]),
										r || (h.push(v), (v = void 0));
								}
							} else v.isBlock || h.push(v);
							if (i && !v?.isBlock) break;
						}
						return (
							h.forEach((i) => {
								let s, r;
								let { body: o, elseBody: a, expression: n, fn: h } = i;
								if (h) {
									let t = [i],
										l = n.split(" ");
									for (r = c[h].length; r--; ) t.unshift(y(l[r + 1]));
									(s = c[h].apply(e, t)),
										i.isBlock && "boolean" == typeof s && (s = p(s ? o : a, e));
								} else {
									let t = n.split(":");
									if (
										((s = y(t.shift() || "")), t.length && "number" == typeof s)
									) {
										let e = t.join(":");
										if (d.test(e)) {
											let t = parseInt((e.match(g) || ["", "-1"])[1], 10);
											null !== s &&
												(s = x(
													s,
													t,
													f.decimalPoint,
													e.indexOf(",") > -1 ? f.thousandsSep : ""
												));
										} else s = m.dateFormat(e, s);
									}
								}
								t = t.replace(i.find, l(s, ""));
							}),
							k ? p(t, e, r) : t
						);
					}
					function u(t, e, s, r) {
						let o, a;
						(t = +t || 0), (e = +e);
						let h = i.lang,
							c = (t.toString().split(".")[1] || "").split("e")[0].length,
							p = t.toString().split("e"),
							u = e;
						-1 === e
							? (e = Math.min(c, 20))
							: n(e)
							? e &&
							  p[1] &&
							  p[1] < 0 &&
							  ((a = e + +p[1]) >= 0
									? ((p[0] = (+p[0]).toExponential(a).split("e")[0]), (e = a))
									: ((p[0] = p[0].split(".")[0] || 0),
									  (t = e < 20 ? (p[0] * Math.pow(10, p[1])).toFixed(e) : 0),
									  (p[1] = 0)))
							: (e = 2);
						let g = (
								Math.abs(p[1] ? p[0] : t) + Math.pow(10, -Math.max(e, c) - 1)
							).toFixed(e),
							f = String(d(g)),
							m = f.length > 3 ? f.length % 3 : 0;
						return (
							(s = l(s, h.decimalPoint)),
							(r = l(r, h.thousandsSep)),
							(o = (t < 0 ? "-" : "") + (m ? f.substr(0, m) + r : "")),
							0 > +p[1] && !u
								? (o = "0")
								: (o += f.substr(m).replace(/(\d{3})(?=\d)/g, "$1" + r)),
							e && (o += s + g.slice(-e)),
							p[1] && 0 != +o && (o += "e" + p[1]),
							o
						);
					}
					return {
						dateFormat: function (t, e, i) {
							return s.dateFormat(t, e, i);
						},
						format: p,
						helpers: c,
						numberFormat: u,
					};
				}
			),
			i(
				e,
				"Core/Renderer/RendererRegistry.js",
				[e["Core/Globals.js"]],
				function (t) {
					var e, i;
					let s;
					return (
						((i = e || (e = {})).rendererTypes = {}),
						(i.getRendererType = function (t = s) {
							return i.rendererTypes[t] || i.rendererTypes[s];
						}),
						(i.registerRendererType = function (e, r, o) {
							(i.rendererTypes[e] = r),
								(!s || o) && ((s = e), (t.Renderer = r));
						}),
						e
					);
				}
			),
			i(
				e,
				"Core/Renderer/RendererUtilities.js",
				[e["Core/Utilities.js"]],
				function (t) {
					var e;
					let { clamp: i, pick: s, pushUnique: r, stableSort: o } = t;
					return (
						((e || (e = {})).distribute = function t(e, a, n) {
							let h = e,
								l = h.reducedLen || a,
								d = (t, e) => t.target - e.target,
								c = [],
								p = e.length,
								u = [],
								g = c.push,
								f,
								m,
								x,
								y = !0,
								b,
								v,
								S = 0,
								k;
							for (f = p; f--; ) S += e[f].size;
							if (S > l) {
								for (
									o(e, (t, e) => (e.rank || 0) - (t.rank || 0)),
										x = (k = e[0].rank === e[e.length - 1].rank) ? p / 2 : -1,
										m = k ? x : p - 1;
									x && S > l;

								)
									(b = e[(f = Math.floor(m))]),
										r(u, f) && (S -= b.size),
										(m += x),
										k && m >= e.length && ((x /= 2), (m = x));
								u.sort((t, e) => e - t).forEach((t) =>
									g.apply(c, e.splice(t, 1))
								);
							}
							for (
								o(e, d),
									e = e.map((t) => ({
										size: t.size,
										targets: [t.target],
										align: s(t.align, 0.5),
									}));
								y;

							) {
								for (f = e.length; f--; )
									(b = e[f]),
										(v =
											(Math.min.apply(0, b.targets) +
												Math.max.apply(0, b.targets)) /
											2),
										(b.pos = i(v - b.size * b.align, 0, a - b.size));
								for (f = e.length, y = !1; f--; )
									f > 0 &&
										e[f - 1].pos + e[f - 1].size > e[f].pos &&
										((e[f - 1].size += e[f].size),
										(e[f - 1].targets = e[f - 1].targets.concat(e[f].targets)),
										(e[f - 1].align = 0.5),
										e[f - 1].pos + e[f - 1].size > a &&
											(e[f - 1].pos = a - e[f - 1].size),
										e.splice(f, 1),
										(y = !0));
							}
							return (
								g.apply(h, c),
								(f = 0),
								e.some((e) => {
									let i = 0;
									return (e.targets || []).some(() =>
										((h[f].pos = e.pos + i),
										void 0 !== n && Math.abs(h[f].pos - h[f].target) > n)
											? (h.slice(0, f + 1).forEach((t) => delete t.pos),
											  (h.reducedLen = (h.reducedLen || a) - 0.1 * a),
											  h.reducedLen > 0.1 * a && t(h, a, n),
											  !0)
											: ((i += h[f].size), f++, !1)
									);
								}),
								o(h, d),
								h
							);
						}),
						e
					);
				}
			),
			i(
				e,
				"Core/Renderer/SVG/SVGElement.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Color/Color.js"],
					e["Core/Globals.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s) {
					let { animate: r, animObject: o, stop: a } = t,
						{ deg2rad: n, doc: h, svg: l, SVG_NS: d, win: c } = i,
						{
							addEvent: p,
							attr: u,
							createElement: g,
							css: f,
							defined: m,
							erase: x,
							extend: y,
							fireEvent: b,
							isArray: v,
							isFunction: S,
							isObject: k,
							isString: C,
							merge: M,
							objectEach: w,
							pick: T,
							pInt: A,
							replaceNested: P,
							syncTimeout: L,
							uniqueKey: O,
						} = s;
					class D {
						_defaultGetter(t) {
							let e = T(
								this[t + "Value"],
								this[t],
								this.element ? this.element.getAttribute(t) : null,
								0
							);
							return /^[\-0-9\.]+$/.test(e) && (e = parseFloat(e)), e;
						}
						_defaultSetter(t, e, i) {
							i.setAttribute(e, t);
						}
						add(t) {
							let e;
							let i = this.renderer,
								s = this.element;
							return (
								t && (this.parentGroup = t),
								void 0 !== this.textStr &&
									"text" === this.element.nodeName &&
									i.buildText(this),
								(this.added = !0),
								(!t || t.handleZ || this.zIndex) && (e = this.zIndexSetter()),
								e || (t ? t.element : i.box).appendChild(s),
								this.onAdd && this.onAdd(),
								this
							);
						}
						addClass(t, e) {
							let i = e ? "" : this.attr("class") || "";
							return (
								(t = (t || "")
									.split(/ /g)
									.reduce(
										function (t, e) {
											return -1 === i.indexOf(e) && t.push(e), t;
										},
										i ? [i] : []
									)
									.join(" ")) !== i && this.attr("class", t),
								this
							);
						}
						afterSetters() {
							this.doTransform &&
								(this.updateTransform(), (this.doTransform = !1));
						}
						align(t, e, i, s = !0) {
							let r, o, a, n, h;
							let l = {},
								d = this.renderer,
								c = d.alignedObjects;
							t
								? ((this.alignOptions = t),
								  (this.alignByTranslate = e),
								  (!i || C(i)) &&
										((this.alignTo = a = i || "renderer"),
										x(c, this),
										c.push(this),
										(i = void 0)))
								: ((t = this.alignOptions),
								  (e = this.alignByTranslate),
								  (a = this.alignTo)),
								(i = T(i, d[a], d));
							let p = t.align,
								u = t.verticalAlign;
							return (
								(r = (i.x || 0) + (t.x || 0)),
								(o = (i.y || 0) + (t.y || 0)),
								"right" === p ? (n = 1) : "center" === p && (n = 2),
								n && (r += (i.width - (t.width || 0)) / n),
								(l[e ? "translateX" : "x"] = Math.round(r)),
								"bottom" === u ? (h = 1) : "middle" === u && (h = 2),
								h && (o += (i.height - (t.height || 0)) / h),
								(l[e ? "translateY" : "y"] = Math.round(o)),
								s &&
									(this[this.placed ? "animate" : "attr"](l),
									(this.placed = !0)),
								(this.alignAttr = l),
								this
							);
						}
						alignSetter(t) {
							let e = { left: "start", center: "middle", right: "end" };
							e[t] &&
								((this.alignValue = t),
								this.element.setAttribute("text-anchor", e[t]));
						}
						animate(t, e, i) {
							let s = o(T(e, this.renderer.globalAnimation, !0)),
								a = s.defer;
							return (
								h.hidden && (s.duration = 0),
								0 !== s.duration
									? (i && (s.complete = i),
									  L(() => {
											this.element && r(this, t, s);
									  }, a))
									: (this.attr(t, void 0, i || s.complete),
									  w(
											t,
											function (t, e) {
												s.step &&
													s.step.call(this, t, { prop: e, pos: 1, elem: this });
											},
											this
									  )),
								this
							);
						}
						applyTextOutline(t) {
							let e = this.element;
							-1 !== t.indexOf("contrast") &&
								(t = t.replace(
									/contrast/g,
									this.renderer.getContrast(e.style.fill)
								));
							let s = t.split(" "),
								r = s[s.length - 1],
								o = s[0];
							if (o && "none" !== o && i.svg) {
								(this.fakeTS = !0),
									(o = o.replace(/(^[\d\.]+)(.*?)$/g, function (t, e, i) {
										return 2 * Number(e) + i;
									})),
									this.removeTextOutline();
								let t = h.createElementNS(d, "tspan");
								u(t, {
									class: "highcharts-text-outline",
									fill: r,
									stroke: r,
									"stroke-width": o,
									"stroke-linejoin": "round",
								});
								let i = e.querySelector("textPath") || e;
								[].forEach.call(i.childNodes, (e) => {
									let i = e.cloneNode(!0);
									i.removeAttribute &&
										["fill", "stroke", "stroke-width", "stroke"].forEach((t) =>
											i.removeAttribute(t)
										),
										t.appendChild(i);
								});
								let s = 0;
								[].forEach.call(i.querySelectorAll("text tspan"), (t) => {
									s += Number(t.getAttribute("dy"));
								});
								let a = h.createElementNS(d, "tspan");
								(a.textContent = "​"),
									u(a, { x: Number(e.getAttribute("x")), dy: -s }),
									t.appendChild(a),
									i.insertBefore(t, i.firstChild);
							}
						}
						attr(t, e, i, s) {
							let r = this.element,
								o = D.symbolCustomAttribs,
								n,
								h,
								l = this,
								d;
							return (
								"string" == typeof t &&
									void 0 !== e &&
									((n = t), ((t = {})[n] = e)),
								"string" == typeof t
									? (l = (this[t + "Getter"] || this._defaultGetter).call(
											this,
											t,
											r
									  ))
									: (w(
											t,
											function (e, i) {
												(d = !1),
													s || a(this, i),
													this.symbolName &&
														-1 !== o.indexOf(i) &&
														(h || (this.symbolAttr(t), (h = !0)), (d = !0)),
													this.rotation &&
														("x" === i || "y" === i) &&
														(this.doTransform = !0),
													d ||
														(this[i + "Setter"] || this._defaultSetter).call(
															this,
															e,
															i,
															r
														);
											},
											this
									  ),
									  this.afterSetters()),
								i && i.call(this),
								l
							);
						}
						clip(t) {
							if (t && !t.clipPath) {
								let e = O() + "-",
									i = this.renderer
										.createElement("clipPath")
										.attr({ id: e })
										.add(this.renderer.defs);
								y(t, { clipPath: i, id: e, count: 0 }), t.add(i);
							}
							return this.attr(
								"clip-path",
								t ? `url(${this.renderer.url}#${t.id})` : "none"
							);
						}
						crisp(t, e) {
							let i = (Math.round((e = e || t.strokeWidth || 0)) % 2) / 2;
							return (
								(t.x = Math.floor(t.x || this.x || 0) + i),
								(t.y = Math.floor(t.y || this.y || 0) + i),
								(t.width = Math.floor((t.width || this.width || 0) - 2 * i)),
								(t.height = Math.floor((t.height || this.height || 0) - 2 * i)),
								m(t.strokeWidth) && (t.strokeWidth = e),
								t
							);
						}
						complexColor(t, i, s) {
							let r = this.renderer,
								o,
								a,
								n,
								h,
								l,
								d,
								c,
								p,
								u,
								g,
								f = [],
								x;
							b(
								this.renderer,
								"complexColor",
								{ args: arguments },
								function () {
									if (
										(t.radialGradient
											? (a = "radialGradient")
											: t.linearGradient && (a = "linearGradient"),
										a)
									) {
										if (
											((n = t[a]),
											(l = r.gradients),
											(d = t.stops),
											(u = s.radialReference),
											v(n) &&
												(t[a] = n =
													{
														x1: n[0],
														y1: n[1],
														x2: n[2],
														y2: n[3],
														gradientUnits: "userSpaceOnUse",
													}),
											"radialGradient" === a &&
												u &&
												!m(n.gradientUnits) &&
												((h = n),
												(n = M(n, r.getRadialAttr(u, h), {
													gradientUnits: "userSpaceOnUse",
												}))),
											w(n, function (t, e) {
												"id" !== e && f.push(e, t);
											}),
											w(d, function (t) {
												f.push(t);
											}),
											l[(f = f.join(","))])
										)
											g = l[f].attr("id");
										else {
											n.id = g = O();
											let t = (l[f] = r.createElement(a).attr(n).add(r.defs));
											(t.radAttr = h),
												(t.stops = []),
												d.forEach(function (i) {
													0 === i[1].indexOf("rgba")
														? ((c = (o = e.parse(i[1])).get("rgb")),
														  (p = o.get("a")))
														: ((c = i[1]), (p = 1));
													let s = r
														.createElement("stop")
														.attr({
															offset: i[0],
															"stop-color": c,
															"stop-opacity": p,
														})
														.add(t);
													t.stops.push(s);
												});
										}
										(x = "url(" + r.url + "#" + g + ")"),
											s.setAttribute(i, x),
											(s.gradient = f),
											(t.toString = function () {
												return x;
											});
									}
								}
							);
						}
						css(t) {
							let e = this.styles,
								i = {},
								s = this.element,
								r,
								o = !e;
							if (
								(e &&
									w(t, function (t, s) {
										e && e[s] !== t && ((i[s] = t), (o = !0));
									}),
								o)
							) {
								e && (t = y(e, i)),
									null === t.width || "auto" === t.width
										? delete this.textWidth
										: "text" === s.nodeName.toLowerCase() &&
										  t.width &&
										  (r = this.textWidth = A(t.width)),
									y(this.styles, t),
									r && !l && this.renderer.forExport && delete t.width;
								let o = M(t);
								s.namespaceURI === this.SVG_NS &&
									(["textOutline", "textOverflow", "width"].forEach(
										(t) => o && delete o[t]
									),
									o.color && (o.fill = o.color)),
									f(s, o);
							}
							return (
								this.added &&
									("text" === this.element.nodeName &&
										this.renderer.buildText(this),
									t.textOutline && this.applyTextOutline(t.textOutline)),
								this
							);
						}
						dashstyleSetter(t) {
							let e,
								i = this["stroke-width"];
							if (("inherit" === i && (i = 1), (t = t && t.toLowerCase()))) {
								let s = t
									.replace("shortdashdotdot", "3,1,1,1,1,1,")
									.replace("shortdashdot", "3,1,1,1")
									.replace("shortdot", "1,1,")
									.replace("shortdash", "3,1,")
									.replace("longdash", "8,3,")
									.replace(/dot/g, "1,3,")
									.replace("dash", "4,3,")
									.replace(/,$/, "")
									.split(",");
								for (e = s.length; e--; ) s[e] = "" + A(s[e]) * T(i, NaN);
								(t = s.join(",").replace(/NaN/g, "none")),
									this.element.setAttribute("stroke-dasharray", t);
							}
						}
						destroy() {
							let t = this,
								e = t.element || {},
								i = t.renderer,
								s = e.ownerSVGElement,
								r = ("SPAN" === e.nodeName && t.parentGroup) || void 0,
								o,
								n;
							if (
								((e.onclick =
									e.onmouseout =
									e.onmouseover =
									e.onmousemove =
									e.point =
										null),
								a(t),
								t.clipPath && s)
							) {
								let e = t.clipPath;
								[].forEach.call(
									s.querySelectorAll("[clip-path],[CLIP-PATH]"),
									function (t) {
										t.getAttribute("clip-path").indexOf(e.element.id) > -1 &&
											t.removeAttribute("clip-path");
									}
								),
									(t.clipPath = e.destroy());
							}
							if (((t.connector = t.connector?.destroy()), t.stops)) {
								for (n = 0; n < t.stops.length; n++) t.stops[n].destroy();
								(t.stops.length = 0), (t.stops = void 0);
							}
							for (
								t.safeRemoveChild(e);
								r && r.div && 0 === r.div.childNodes.length;

							)
								(o = r.parentGroup),
									t.safeRemoveChild(r.div),
									delete r.div,
									(r = o);
							t.alignTo && x(i.alignedObjects, t),
								w(t, function (e, i) {
									t[i] &&
										t[i].parentGroup === t &&
										t[i].destroy &&
										t[i].destroy(),
										delete t[i];
								});
						}
						dSetter(t, e, i) {
							v(t) &&
								("string" == typeof t[0] &&
									(t = this.renderer.pathToSegments(t)),
								(this.pathArray = t),
								(t = t.reduce(
									(t, e, i) =>
										e && e.join
											? (i ? t + " " : "") + e.join(" ")
											: (e || "").toString(),
									""
								))),
								/(NaN| {2}|^$)/.test(t) && (t = "M 0 0"),
								this[e] !== t && (i.setAttribute(e, t), (this[e] = t));
						}
						fillSetter(t, e, i) {
							"string" == typeof t
								? i.setAttribute(e, t)
								: t && this.complexColor(t, e, i);
						}
						hrefSetter(t, e, i) {
							i.setAttributeNS("http://www.w3.org/1999/xlink", e, t);
						}
						getBBox(t, e) {
							let i, s, r, o;
							let {
									alignValue: a,
									element: n,
									renderer: h,
									styles: l,
									textStr: d,
								} = this,
								{ cache: c, cacheKeys: p } = h,
								u = n.namespaceURI === this.SVG_NS,
								g = T(e, this.rotation, 0),
								x = h.styledMode
									? n && D.prototype.getStyle.call(n, "font-size")
									: l.fontSize;
							if (
								(m(d) &&
									(-1 === (o = d.toString()).indexOf("<") &&
										(o = o.replace(/[0-9]/g, "0")),
									(o += [
										"",
										h.rootFontSize,
										x,
										g,
										this.textWidth,
										a,
										l.textOverflow,
										l.fontWeight,
									].join(","))),
								o && !t && (i = c[o]),
								!i)
							) {
								if (u || h.forExport) {
									try {
										(r =
											this.fakeTS &&
											function (t) {
												let e = n.querySelector(".highcharts-text-outline");
												e && f(e, { display: t });
											}),
											S(r) && r("none"),
											(i = n.getBBox
												? y({}, n.getBBox())
												: {
														width: n.offsetWidth,
														height: n.offsetHeight,
														x: 0,
														y: 0,
												  }),
											S(r) && r("");
									} catch (t) {}
									(!i || i.width < 0) &&
										(i = { x: 0, y: 0, width: 0, height: 0 });
								} else i = this.htmlGetBBox();
								(s = i.height),
									u &&
										(i.height = s =
											{ "11px,17": 14, "13px,20": 16 }[
												`${x || ""},${Math.round(s)}`
											] || s),
									g && (i = this.getRotatedBox(i, g));
							}
							if (o && ("" === d || i.height > 0)) {
								for (; p.length > 250; ) delete c[p.shift()];
								c[o] || p.push(o), (c[o] = i);
							}
							return i;
						}
						getRotatedBox(t, e) {
							let { x: i, y: s, width: r, height: o } = t,
								{
									alignValue: a,
									translateY: h,
									rotationOriginX: l = 0,
									rotationOriginY: d = 0,
								} = this,
								c = { right: 1, center: 0.5 }[a || 0] || 0,
								p = Number(this.element.getAttribute("y") || 0) - (h ? 0 : s),
								u = e * n,
								g = (e - 90) * n,
								f = Math.cos(u),
								m = Math.sin(u),
								x = r * f,
								y = r * m,
								b = Math.cos(g),
								v = Math.sin(g),
								[[S, k], [C, M]] = [l, d].map((t) => [t - t * f, t * m]),
								w = i + c * (r - x) + S + M + p * b,
								T = w + x,
								A = T - o * b,
								P = A - x,
								L = s + p - c * y - k + C + p * v,
								O = L + y,
								D = O - o * v,
								E = D - y,
								I = Math.min(w, T, A, P),
								j = Math.min(L, O, D, E),
								B = Math.max(w, T, A, P) - I,
								R = Math.max(L, O, D, E) - j;
							return { x: I, y: j, width: B, height: R };
						}
						getStyle(t) {
							return c
								.getComputedStyle(this.element || this, "")
								.getPropertyValue(t);
						}
						hasClass(t) {
							return -1 !== ("" + this.attr("class")).split(" ").indexOf(t);
						}
						hide() {
							return this.attr({ visibility: "hidden" });
						}
						htmlGetBBox() {
							return { height: 0, width: 0, x: 0, y: 0 };
						}
						constructor(t, e) {
							(this.onEvents = {}),
								(this.opacity = 1),
								(this.SVG_NS = d),
								(this.element =
									"span" === e || "body" === e
										? g(e)
										: h.createElementNS(this.SVG_NS, e)),
								(this.renderer = t),
								(this.styles = {}),
								b(this, "afterInit");
						}
						on(t, e) {
							let { onEvents: i } = this;
							return i[t] && i[t](), (i[t] = p(this.element, t, e)), this;
						}
						opacitySetter(t, e, i) {
							let s = Number(Number(t).toFixed(3));
							(this.opacity = s), i.setAttribute(e, s);
						}
						removeClass(t) {
							return this.attr(
								"class",
								("" + this.attr("class"))
									.replace(C(t) ? RegExp(`(^| )${t}( |$)`) : t, " ")
									.replace(/ +/g, " ")
									.trim()
							);
						}
						removeTextOutline() {
							let t = this.element.querySelector(
								"tspan.highcharts-text-outline"
							);
							t && this.safeRemoveChild(t);
						}
						safeRemoveChild(t) {
							let e = t.parentNode;
							e && e.removeChild(t);
						}
						setRadialReference(t) {
							let e =
								this.element.gradient &&
								this.renderer.gradients[this.element.gradient];
							return (
								(this.element.radialReference = t),
								e &&
									e.radAttr &&
									e.animate(this.renderer.getRadialAttr(t, e.radAttr)),
								this
							);
						}
						setTextPath(t, e) {
							e = M(
								!0,
								{
									enabled: !0,
									attributes: {
										dy: -5,
										startOffset: "50%",
										textAnchor: "middle",
									},
								},
								e
							);
							let i = this.renderer.url,
								s = this.text || this,
								r = s.textPath,
								{ attributes: o, enabled: a } = e;
							if (((t = t || (r && r.path)), r && r.undo(), t && a)) {
								let e = p(s, "afterModifyTree", (e) => {
									if (t && a) {
										let r = t.attr("id");
										r || t.attr("id", (r = O()));
										let a = { x: 0, y: 0 };
										m(o.dx) && ((a.dx = o.dx), delete o.dx),
											m(o.dy) && ((a.dy = o.dy), delete o.dy),
											s.attr(a),
											this.attr({ transform: "" }),
											this.box && (this.box = this.box.destroy());
										let n = e.nodes.slice(0);
										(e.nodes.length = 0),
											(e.nodes[0] = {
												tagName: "textPath",
												attributes: y(o, {
													"text-anchor": o.textAnchor,
													href: `${i}#${r}`,
												}),
												children: n,
											});
									}
								});
								s.textPath = { path: t, undo: e };
							} else s.attr({ dx: 0, dy: 0 }), delete s.textPath;
							return (
								this.added && ((s.textCache = ""), this.renderer.buildText(s)),
								this
							);
						}
						shadow(t) {
							let { renderer: e } = this,
								i = M(
									this.parentGroup?.rotation === 90
										? { offsetX: -1, offsetY: -1 }
										: {},
									k(t) ? t : {}
								),
								s = e.shadowDefinition(i);
							return this.attr({ filter: t ? `url(${e.url}#${s})` : "none" });
						}
						show(t = !0) {
							return this.attr({ visibility: t ? "inherit" : "visible" });
						}
						"stroke-widthSetter"(t, e, i) {
							(this[e] = t), i.setAttribute(e, t);
						}
						strokeWidth() {
							if (!this.renderer.styledMode) return this["stroke-width"] || 0;
							let t = this.getStyle("stroke-width"),
								e = 0,
								i;
							return (
								/px$/.test(t)
									? (e = A(t))
									: "" !== t &&
									  (u((i = h.createElementNS(d, "rect")), {
											width: t,
											"stroke-width": 0,
									  }),
									  this.element.parentNode.appendChild(i),
									  (e = i.getBBox().width),
									  i.parentNode.removeChild(i)),
								e
							);
						}
						symbolAttr(t) {
							let e = this;
							D.symbolCustomAttribs.forEach(function (i) {
								e[i] = T(t[i], e[i]);
							}),
								e.attr({
									d: e.renderer.symbols[e.symbolName](
										e.x,
										e.y,
										e.width,
										e.height,
										e
									),
								});
						}
						textSetter(t) {
							t !== this.textStr &&
								(delete this.textPxLength,
								(this.textStr = t),
								this.added && this.renderer.buildText(this));
						}
						titleSetter(t) {
							let e = this.element,
								i =
									e.getElementsByTagName("title")[0] ||
									h.createElementNS(this.SVG_NS, "title");
							e.insertBefore
								? e.insertBefore(i, e.firstChild)
								: e.appendChild(i),
								(i.textContent = P(T(t, ""), [/<[^>]*>/g, ""])
									.replace(/&lt;/g, "<")
									.replace(/&gt;/g, ">"));
						}
						toFront() {
							let t = this.element;
							return t.parentNode.appendChild(t), this;
						}
						translate(t, e) {
							return this.attr({ translateX: t, translateY: e });
						}
						updateTransform(t = "transform") {
							let {
									element: e,
									matrix: i,
									rotation: s = 0,
									rotationOriginX: r,
									rotationOriginY: o,
									scaleX: a,
									scaleY: n,
									translateX: h = 0,
									translateY: l = 0,
								} = this,
								d = ["translate(" + h + "," + l + ")"];
							m(i) && d.push("matrix(" + i.join(",") + ")"),
								s &&
									(d.push(
										"rotate(" +
											s +
											" " +
											T(r, e.getAttribute("x"), 0) +
											" " +
											T(o, e.getAttribute("y") || 0) +
											")"
									),
									this.text?.element.tagName === "SPAN" &&
										this.text.attr({
											rotation: s,
											rotationOriginX: (r || 0) - this.padding,
											rotationOriginY: (o || 0) - this.padding,
										})),
								(m(a) || m(n)) &&
									d.push("scale(" + T(a, 1) + " " + T(n, 1) + ")"),
								d.length &&
									!(this.text || this).textPath &&
									e.setAttribute(t, d.join(" "));
						}
						visibilitySetter(t, e, i) {
							"inherit" === t
								? i.removeAttribute(e)
								: this[e] !== t && i.setAttribute(e, t),
								(this[e] = t);
						}
						xGetter(t) {
							return (
								"circle" === this.element.nodeName &&
									("x" === t ? (t = "cx") : "y" === t && (t = "cy")),
								this._defaultGetter(t)
							);
						}
						zIndexSetter(t, e) {
							let i = this.renderer,
								s = this.parentGroup,
								r = (s || i).element || i.box,
								o = this.element,
								a = r === i.box,
								n,
								h,
								l,
								d = !1,
								c,
								p = this.added,
								u;
							if (
								(m(t)
									? (o.setAttribute("data-z-index", t),
									  (t = +t),
									  this[e] === t && (p = !1))
									: m(this[e]) && o.removeAttribute("data-z-index"),
								(this[e] = t),
								p)
							) {
								for (
									(t = this.zIndex) && s && (s.handleZ = !0),
										u = (n = r.childNodes).length - 1;
									u >= 0 && !d;
									u--
								)
									(c = !m((l = (h = n[u]).getAttribute("data-z-index")))),
										h !== o &&
											(t < 0 && c && !a && !u
												? (r.insertBefore(o, n[u]), (d = !0))
												: (A(l) <= t || (c && (!m(t) || t >= 0))) &&
												  (r.insertBefore(o, n[u + 1]), (d = !0)));
								d || (r.insertBefore(o, n[a ? 3 : 0]), (d = !0));
							}
							return d;
						}
					}
					return (
						(D.symbolCustomAttribs = [
							"anchorX",
							"anchorY",
							"clockwise",
							"end",
							"height",
							"innerR",
							"r",
							"start",
							"width",
							"x",
							"y",
						]),
						(D.prototype.strokeSetter = D.prototype.fillSetter),
						(D.prototype.yGetter = D.prototype.xGetter),
						(D.prototype.matrixSetter =
							D.prototype.rotationOriginXSetter =
							D.prototype.rotationOriginYSetter =
							D.prototype.rotationSetter =
							D.prototype.scaleXSetter =
							D.prototype.scaleYSetter =
							D.prototype.translateXSetter =
							D.prototype.translateYSetter =
							D.prototype.verticalAlignSetter =
								function (t, e) {
									(this[e] = t), (this.doTransform = !0);
								}),
						D
					);
				}
			),
			i(
				e,
				"Core/Renderer/SVG/SVGLabel.js",
				[e["Core/Renderer/SVG/SVGElement.js"], e["Core/Utilities.js"]],
				function (t, e) {
					let {
						defined: i,
						extend: s,
						isNumber: r,
						merge: o,
						pick: a,
						removeEvent: n,
					} = e;
					class h extends t {
						constructor(t, e, i, s, r, o, a, n, l, d) {
							let c;
							super(t, "g"),
								(this.paddingLeftSetter = this.paddingSetter),
								(this.paddingRightSetter = this.paddingSetter),
								(this.textStr = e),
								(this.x = i),
								(this.y = s),
								(this.anchorX = o),
								(this.anchorY = a),
								(this.baseline = l),
								(this.className = d),
								this.addClass(
									"button" === d ? "highcharts-no-tooltip" : "highcharts-label"
								),
								d && this.addClass("highcharts-" + d),
								(this.text = t.text(void 0, 0, 0, n).attr({ zIndex: 1 })),
								"string" == typeof r &&
									((c = /^url\((.*?)\)$/.test(r)) ||
										this.renderer.symbols[r]) &&
									(this.symbolKey = r),
								(this.bBox = h.emptyBBox),
								(this.padding = 3),
								(this.baselineOffset = 0),
								(this.needsBox = t.styledMode || c),
								(this.deferredAttr = {}),
								(this.alignFactor = 0);
						}
						alignSetter(t) {
							let e = { left: 0, center: 0.5, right: 1 }[t];
							e !== this.alignFactor &&
								((this.alignFactor = e),
								this.bBox &&
									r(this.xSetting) &&
									this.attr({ x: this.xSetting }));
						}
						anchorXSetter(t, e) {
							(this.anchorX = t),
								this.boxAttr(
									e,
									Math.round(t) - this.getCrispAdjust() - this.xSetting
								);
						}
						anchorYSetter(t, e) {
							(this.anchorY = t), this.boxAttr(e, t - this.ySetting);
						}
						boxAttr(t, e) {
							this.box ? this.box.attr(t, e) : (this.deferredAttr[t] = e);
						}
						css(e) {
							if (e) {
								let t = {};
								(e = o(e)),
									h.textProps.forEach((i) => {
										void 0 !== e[i] && ((t[i] = e[i]), delete e[i]);
									}),
									this.text.css(t),
									"fontSize" in t || "fontWeight" in t
										? this.updateTextPadding()
										: ("width" in t || "textOverflow" in t) &&
										  this.updateBoxSize();
							}
							return t.prototype.css.call(this, e);
						}
						destroy() {
							n(this.element, "mouseenter"),
								n(this.element, "mouseleave"),
								this.text && this.text.destroy(),
								this.box && (this.box = this.box.destroy()),
								t.prototype.destroy.call(this);
						}
						fillSetter(t, e) {
							t && (this.needsBox = !0), (this.fill = t), this.boxAttr(e, t);
						}
						getBBox(t, e) {
							this.textStr &&
								0 === this.bBox.width &&
								0 === this.bBox.height &&
								this.updateBoxSize();
							let {
									padding: i,
									height: s = 0,
									translateX: r = 0,
									translateY: o = 0,
									width: n = 0,
								} = this,
								h = a(this.paddingLeft, i),
								l = e ?? (this.rotation || 0),
								d = {
									width: n,
									height: s,
									x: r + this.bBox.x - h,
									y: o + this.bBox.y - i + this.baselineOffset,
								};
							return l && (d = this.getRotatedBox(d, l)), d;
						}
						getCrispAdjust() {
							return this.renderer.styledMode && this.box
								? (this.box.strokeWidth() % 2) / 2
								: ((this["stroke-width"]
										? parseInt(this["stroke-width"], 10)
										: 0) %
										2) /
										2;
						}
						heightSetter(t) {
							this.heightSetting = t;
						}
						onAdd() {
							this.text.add(this),
								this.attr({
									text: a(this.textStr, ""),
									x: this.x || 0,
									y: this.y || 0,
								}),
								this.box &&
									i(this.anchorX) &&
									this.attr({ anchorX: this.anchorX, anchorY: this.anchorY });
						}
						paddingSetter(t, e) {
							r(t)
								? t !== this[e] && ((this[e] = t), this.updateTextPadding())
								: (this[e] = void 0);
						}
						rSetter(t, e) {
							this.boxAttr(e, t);
						}
						strokeSetter(t, e) {
							(this.stroke = t), this.boxAttr(e, t);
						}
						"stroke-widthSetter"(t, e) {
							t && (this.needsBox = !0),
								(this["stroke-width"] = t),
								this.boxAttr(e, t);
						}
						"text-alignSetter"(t) {
							this.textAlign = t;
						}
						textSetter(t) {
							void 0 !== t && this.text.attr({ text: t }),
								this.updateTextPadding();
						}
						updateBoxSize() {
							let t;
							let e = this.text,
								o = {},
								a = this.padding,
								n = (this.bBox =
									(!r(this.widthSetting) ||
										!r(this.heightSetting) ||
										this.textAlign) &&
									i(e.textStr)
										? e.getBBox(void 0, 0)
										: h.emptyBBox);
							(this.width = this.getPaddedWidth()),
								(this.height = (this.heightSetting || n.height || 0) + 2 * a);
							let l = this.renderer.fontMetrics(e);
							if (
								((this.baselineOffset =
									a +
									Math.min(
										(this.text.firstLineMetrics || l).b,
										n.height || 1 / 0
									)),
								this.heightSetting &&
									(this.baselineOffset += (this.heightSetting - l.h) / 2),
								this.needsBox && !e.textPath)
							) {
								if (!this.box) {
									let t = (this.box = this.symbolKey
										? this.renderer.symbol(this.symbolKey)
										: this.renderer.rect());
									t.addClass(
										("button" === this.className
											? ""
											: "highcharts-label-box") +
											(this.className
												? " highcharts-" + this.className + "-box"
												: "")
									),
										t.add(this);
								}
								(t = this.getCrispAdjust()),
									(o.x = t),
									(o.y = (this.baseline ? -this.baselineOffset : 0) + t),
									(o.width = Math.round(this.width)),
									(o.height = Math.round(this.height)),
									this.box.attr(s(o, this.deferredAttr)),
									(this.deferredAttr = {});
							}
						}
						updateTextPadding() {
							let t = this.text;
							if (!t.textPath) {
								this.updateBoxSize();
								let e = this.baseline ? 0 : this.baselineOffset,
									s = a(this.paddingLeft, this.padding);
								i(this.widthSetting) &&
									this.bBox &&
									("center" === this.textAlign || "right" === this.textAlign) &&
									(s +=
										{ center: 0.5, right: 1 }[this.textAlign] *
										(this.widthSetting - this.bBox.width)),
									(s !== t.x || e !== t.y) &&
										(t.attr("x", s),
										t.hasBoxWidthChanged && (this.bBox = t.getBBox(!0)),
										void 0 !== e && t.attr("y", e)),
									(t.x = s),
									(t.y = e);
							}
						}
						widthSetter(t) {
							this.widthSetting = r(t) ? t : void 0;
						}
						getPaddedWidth() {
							let t = this.padding,
								e = a(this.paddingLeft, t),
								i = a(this.paddingRight, t);
							return (this.widthSetting || this.bBox.width || 0) + e + i;
						}
						xSetter(t) {
							(this.x = t),
								this.alignFactor &&
									((t -= this.alignFactor * this.getPaddedWidth()),
									(this["forceAnimate:x"] = !0)),
								(this.xSetting = Math.round(t)),
								this.attr("translateX", this.xSetting);
						}
						ySetter(t) {
							(this.ySetting = this.y = Math.round(t)),
								this.attr("translateY", this.ySetting);
						}
					}
					return (
						(h.emptyBBox = { width: 0, height: 0, x: 0, y: 0 }),
						(h.textProps = [
							"color",
							"direction",
							"fontFamily",
							"fontSize",
							"fontStyle",
							"fontWeight",
							"lineHeight",
							"textAlign",
							"textDecoration",
							"textOutline",
							"textOverflow",
							"whiteSpace",
							"width",
						]),
						h
					);
				}
			),
			i(
				e,
				"Core/Renderer/SVG/Symbols.js",
				[e["Core/Utilities.js"]],
				function (t) {
					let { defined: e, isNumber: i, pick: s } = t;
					function r(t, i, r, o, a) {
						let n = [];
						if (a) {
							let h = a.start || 0,
								l = s(a.r, r),
								d = s(a.r, o || r),
								c = 0.001 > Math.abs((a.end || 0) - h - 2 * Math.PI),
								p = (a.end || 0) - 0.001,
								u = a.innerR,
								g = s(a.open, c),
								f = Math.cos(h),
								m = Math.sin(h),
								x = Math.cos(p),
								y = Math.sin(p),
								b = s(a.longArc, p - h - Math.PI < 0.001 ? 0 : 1),
								v = ["A", l, d, 0, b, s(a.clockwise, 1), t + l * x, i + d * y];
							(v.params = { start: h, end: p, cx: t, cy: i }),
								n.push(["M", t + l * f, i + d * m], v),
								e(u) &&
									(((v = [
										"A",
										u,
										u,
										0,
										b,
										e(a.clockwise) ? 1 - a.clockwise : 0,
										t + u * f,
										i + u * m,
									]).params = { start: p, end: h, cx: t, cy: i }),
									n.push(
										g
											? ["M", t + u * x, i + u * y]
											: ["L", t + u * x, i + u * y],
										v
									)),
								g || n.push(["Z"]);
						}
						return n;
					}
					function o(t, e, i, s, r) {
						return r && r.r
							? a(t, e, i, s, r)
							: [
									["M", t, e],
									["L", t + i, e],
									["L", t + i, e + s],
									["L", t, e + s],
									["Z"],
							  ];
					}
					function a(t, e, i, s, r) {
						let o = r?.r || 0;
						return [
							["M", t + o, e],
							["L", t + i - o, e],
							["A", o, o, 0, 0, 1, t + i, e + o],
							["L", t + i, e + s - o],
							["A", o, o, 0, 0, 1, t + i - o, e + s],
							["L", t + o, e + s],
							["A", o, o, 0, 0, 1, t, e + s - o],
							["L", t, e + o],
							["A", o, o, 0, 0, 1, t + o, e],
							["Z"],
						];
					}
					return {
						arc: r,
						callout: function (t, e, s, r, o) {
							let n = Math.min((o && o.r) || 0, s, r),
								h = n + 6,
								l = o && o.anchorX,
								d = (o && o.anchorY) || 0,
								c = a(t, e, s, r, { r: n });
							if (!i(l) || (l < s && l > 0 && d < r && d > 0)) return c;
							if (t + l > s - h) {
								if (d > e + h && d < e + r - h)
									c.splice(
										3,
										1,
										["L", t + s, d - 6],
										["L", t + s + 6, d],
										["L", t + s, d + 6],
										["L", t + s, e + r - n]
									);
								else if (l < s) {
									let i = d < e + h,
										o = i ? e : e + r;
									c.splice(i ? 2 : 5, 0, ["L", l, d], ["L", t + s - n, o]);
								} else
									c.splice(
										3,
										1,
										["L", t + s, r / 2],
										["L", l, d],
										["L", t + s, r / 2],
										["L", t + s, e + r - n]
									);
							} else if (t + l < h) {
								if (d > e + h && d < e + r - h)
									c.splice(
										7,
										1,
										["L", t, d + 6],
										["L", t - 6, d],
										["L", t, d - 6],
										["L", t, e + n]
									);
								else if (l > 0) {
									let i = d < e + h,
										s = i ? e : e + r;
									c.splice(i ? 1 : 6, 0, ["L", l, d], ["L", t + n, s]);
								} else
									c.splice(
										7,
										1,
										["L", t, r / 2],
										["L", l, d],
										["L", t, r / 2],
										["L", t, e + n]
									);
							} else
								d > r && l < s - h
									? c.splice(
											5,
											1,
											["L", l + 6, e + r],
											["L", l, e + r + 6],
											["L", l - 6, e + r],
											["L", t + n, e + r]
									  )
									: d < 0 &&
									  l > h &&
									  c.splice(
											1,
											1,
											["L", l - 6, e],
											["L", l, e - 6],
											["L", l + 6, e],
											["L", s - n, e]
									  );
							return c;
						},
						circle: function (t, e, i, s) {
							return r(t + i / 2, e + s / 2, i / 2, s / 2, {
								start: 0.5 * Math.PI,
								end: 2.5 * Math.PI,
								open: !1,
							});
						},
						diamond: function (t, e, i, s) {
							return [
								["M", t + i / 2, e],
								["L", t + i, e + s / 2],
								["L", t + i / 2, e + s],
								["L", t, e + s / 2],
								["Z"],
							];
						},
						rect: o,
						roundedRect: a,
						square: o,
						triangle: function (t, e, i, s) {
							return [
								["M", t + i / 2, e],
								["L", t + i, e + s],
								["L", t, e + s],
								["Z"],
							];
						},
						"triangle-down": function (t, e, i, s) {
							return [
								["M", t, e],
								["L", t + i, e],
								["L", t + i / 2, e + s],
								["Z"],
							];
						},
					};
				}
			),
			i(
				e,
				"Core/Renderer/SVG/TextBuilder.js",
				[
					e["Core/Renderer/HTML/AST.js"],
					e["Core/Globals.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					let { doc: s, SVG_NS: r, win: o } = e,
						{
							attr: a,
							extend: n,
							fireEvent: h,
							isString: l,
							objectEach: d,
							pick: c,
						} = i;
					return class {
						constructor(t) {
							let e = t.styles;
							(this.renderer = t.renderer),
								(this.svgElement = t),
								(this.width = t.textWidth),
								(this.textLineHeight = e && e.lineHeight),
								(this.textOutline = e && e.textOutline),
								(this.ellipsis = !!(e && "ellipsis" === e.textOverflow)),
								(this.noWrap = !!(e && "nowrap" === e.whiteSpace));
						}
						buildSVG() {
							let e = this.svgElement,
								i = e.element,
								r = e.renderer,
								o = c(e.textStr, "").toString(),
								a = -1 !== o.indexOf("<"),
								n = i.childNodes,
								h = !e.added && r.box,
								d = [
									o,
									this.ellipsis,
									this.noWrap,
									this.textLineHeight,
									this.textOutline,
									e.getStyle("font-size"),
									this.width,
								].join(",");
							if (d !== e.textCache) {
								(e.textCache = d), delete e.actualWidth;
								for (let t = n.length; t--; ) i.removeChild(n[t]);
								if (
									a ||
									this.ellipsis ||
									this.width ||
									e.textPath ||
									(-1 !== o.indexOf(" ") &&
										(!this.noWrap || /<br.*?>/g.test(o)))
								) {
									if ("" !== o) {
										h && h.appendChild(i);
										let s = new t(o);
										this.modifyTree(s.nodes),
											s.addToDOM(i),
											this.modifyDOM(),
											this.ellipsis &&
												-1 !== (i.textContent || "").indexOf("…") &&
												e.attr(
													"title",
													this.unescapeEntities(e.textStr || "", [
														"&lt;",
														"&gt;",
													])
												),
											h && h.removeChild(i);
									}
								} else
									i.appendChild(s.createTextNode(this.unescapeEntities(o)));
								l(this.textOutline) &&
									e.applyTextOutline &&
									e.applyTextOutline(this.textOutline);
							}
						}
						modifyDOM() {
							let t;
							let e = this.svgElement,
								i = a(e.element, "x");
							for (e.firstLineMetrics = void 0; (t = e.element.firstChild); )
								if (/^[\s\u200B]*$/.test(t.textContent || " "))
									e.element.removeChild(t);
								else break;
							[].forEach.call(
								e.element.querySelectorAll("tspan.highcharts-br"),
								(t, s) => {
									t.nextSibling &&
										t.previousSibling &&
										(0 === s &&
											1 === t.previousSibling.nodeType &&
											(e.firstLineMetrics = e.renderer.fontMetrics(
												t.previousSibling
											)),
										a(t, { dy: this.getLineHeight(t.nextSibling), x: i }));
								}
							);
							let n = this.width || 0;
							if (!n) return;
							let h = (t, o) => {
									let h = t.textContent || "",
										l = h.replace(/([^\^])-/g, "$1- ").split(" "),
										d =
											!this.noWrap &&
											(l.length > 1 || e.element.childNodes.length > 1),
										c = this.getLineHeight(o),
										p = 0,
										u = e.actualWidth;
									if (this.ellipsis)
										h &&
											this.truncate(
												t,
												h,
												void 0,
												0,
												Math.max(0, n - 0.8 * c),
												(t, e) => t.substring(0, e) + "…"
											);
									else if (d) {
										let h = [],
											d = [];
										for (; o.firstChild && o.firstChild !== t; )
											d.push(o.firstChild), o.removeChild(o.firstChild);
										for (; l.length; )
											l.length &&
												!this.noWrap &&
												p > 0 &&
												(h.push(t.textContent || ""),
												(t.textContent = l.join(" ").replace(/- /g, "-"))),
												this.truncate(
													t,
													void 0,
													l,
													(0 === p && u) || 0,
													n,
													(t, e) => l.slice(0, e).join(" ").replace(/- /g, "-")
												),
												(u = e.actualWidth),
												p++;
										d.forEach((e) => {
											o.insertBefore(e, t);
										}),
											h.forEach((e) => {
												o.insertBefore(s.createTextNode(e), t);
												let n = s.createElementNS(r, "tspan");
												(n.textContent = "​"),
													a(n, { dy: c, x: i }),
													o.insertBefore(n, t);
											});
									}
								},
								l = (t) => {
									[].slice.call(t.childNodes).forEach((i) => {
										i.nodeType === o.Node.TEXT_NODE
											? h(i, t)
											: (-1 !== i.className.baseVal.indexOf("highcharts-br") &&
													(e.actualWidth = 0),
											  l(i));
									});
								};
							l(e.element);
						}
						getLineHeight(t) {
							let e = t.nodeType === o.Node.TEXT_NODE ? t.parentElement : t;
							return this.textLineHeight
								? parseInt(this.textLineHeight.toString(), 10)
								: this.renderer.fontMetrics(e || this.svgElement.element).h;
						}
						modifyTree(t) {
							let e = (i, s) => {
								let {
										attributes: r = {},
										children: o,
										style: a = {},
										tagName: h,
									} = i,
									l = this.renderer.styledMode;
								if (
									("b" === h || "strong" === h
										? l
											? (r.class = "highcharts-strong")
											: (a.fontWeight = "bold")
										: ("i" === h || "em" === h) &&
										  (l
												? (r.class = "highcharts-emphasized")
												: (a.fontStyle = "italic")),
									a && a.color && (a.fill = a.color),
									"br" === h)
								) {
									(r.class = "highcharts-br"), (i.textContent = "​");
									let e = t[s + 1];
									e &&
										e.textContent &&
										(e.textContent = e.textContent.replace(/^ +/gm, ""));
								} else
									"a" === h &&
										o &&
										o.some((t) => "#text" === t.tagName) &&
										(i.children = [{ children: o, tagName: "tspan" }]);
								"#text" !== h && "a" !== h && (i.tagName = "tspan"),
									n(i, { attributes: r, style: a }),
									o && o.filter((t) => "#text" !== t.tagName).forEach(e);
							};
							t.forEach(e), h(this.svgElement, "afterModifyTree", { nodes: t });
						}
						truncate(t, e, i, s, r, o) {
							let a, n;
							let h = this.svgElement,
								{ rotation: l } = h,
								d = [],
								c = i ? 1 : 0,
								p = (e || i || "").length,
								u = p,
								g = function (e, r) {
									let o = r || e,
										a = t.parentNode;
									if (a && void 0 === d[o] && a.getSubStringLength)
										try {
											d[o] = s + a.getSubStringLength(0, i ? o + 1 : o);
										} catch (t) {}
									return d[o];
								};
							if (((h.rotation = 0), s + (n = g(t.textContent.length)) > r)) {
								for (; c <= p; )
									(u = Math.ceil((c + p) / 2)),
										i && (a = o(i, u)),
										(n = g(u, a && a.length - 1)),
										c === p ? (c = p + 1) : n > r ? (p = u - 1) : (c = u);
								0 === p
									? (t.textContent = "")
									: (e && p === e.length - 1) ||
									  (t.textContent = a || o(e || i, u));
							}
							i && i.splice(0, u), (h.actualWidth = n), (h.rotation = l);
						}
						unescapeEntities(t, e) {
							return (
								d(this.renderer.escapes, function (i, s) {
									(e && -1 !== e.indexOf(i)) ||
										(t = t.toString().replace(RegExp(i, "g"), s));
								}),
								t
							);
						}
					};
				}
			),
			i(
				e,
				"Core/Renderer/SVG/SVGRenderer.js",
				[
					e["Core/Renderer/HTML/AST.js"],
					e["Core/Color/Color.js"],
					e["Core/Globals.js"],
					e["Core/Renderer/RendererRegistry.js"],
					e["Core/Renderer/SVG/SVGElement.js"],
					e["Core/Renderer/SVG/SVGLabel.js"],
					e["Core/Renderer/SVG/Symbols.js"],
					e["Core/Renderer/SVG/TextBuilder.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r, o, a, n, h) {
					let l;
					let {
							charts: d,
							deg2rad: c,
							doc: p,
							isFirefox: u,
							isMS: g,
							isWebKit: f,
							noop: m,
							SVG_NS: x,
							symbolSizes: y,
							win: b,
						} = i,
						{
							addEvent: v,
							attr: S,
							createElement: k,
							css: C,
							defined: M,
							destroyObjectProperties: w,
							extend: T,
							isArray: A,
							isNumber: P,
							isObject: L,
							isString: O,
							merge: D,
							pick: E,
							pInt: I,
							replaceNested: j,
							uniqueKey: B,
						} = h;
					class R {
						constructor(t, e, i, s, r, o, a) {
							let n, h;
							let l = this.createElement("svg").attr({
									version: "1.1",
									class: "highcharts-root",
								}),
								d = l.element;
							a || l.css(this.getStyle(s || {})),
								t.appendChild(d),
								S(t, "dir", "ltr"),
								-1 === t.innerHTML.indexOf("xmlns") &&
									S(d, "xmlns", this.SVG_NS),
								(this.box = d),
								(this.boxWrapper = l),
								(this.alignedObjects = []),
								(this.url = this.getReferenceURL()),
								this.createElement("desc")
									.add()
									.element.appendChild(
										p.createTextNode("Created with Highcharts 11.4.0")
									),
								(this.defs = this.createElement("defs").add()),
								(this.allowHTML = o),
								(this.forExport = r),
								(this.styledMode = a),
								(this.gradients = {}),
								(this.cache = {}),
								(this.cacheKeys = []),
								(this.imgCount = 0),
								(this.rootFontSize = l.getStyle("font-size")),
								this.setSize(e, i, !1),
								u &&
									t.getBoundingClientRect &&
									((n = function () {
										C(t, { left: 0, top: 0 }),
											(h = t.getBoundingClientRect()),
											C(t, {
												left: Math.ceil(h.left) - h.left + "px",
												top: Math.ceil(h.top) - h.top + "px",
											});
									})(),
									(this.unSubPixelFix = v(b, "resize", n)));
						}
						definition(e) {
							return new t([e]).addToDOM(this.defs.element);
						}
						getReferenceURL() {
							if ((u || f) && p.getElementsByTagName("base").length) {
								if (!M(l)) {
									let e = B(),
										i = new t([
											{
												tagName: "svg",
												attributes: { width: 8, height: 8 },
												children: [
													{
														tagName: "defs",
														children: [
															{
																tagName: "clipPath",
																attributes: { id: e },
																children: [
																	{
																		tagName: "rect",
																		attributes: { width: 4, height: 4 },
																	},
																],
															},
														],
													},
													{
														tagName: "rect",
														attributes: {
															id: "hitme",
															width: 8,
															height: 8,
															"clip-path": `url(#${e})`,
															fill: "rgba(0,0,0,0.001)",
														},
													},
												],
											},
										]).addToDOM(p.body);
									C(i, { position: "fixed", top: 0, left: 0, zIndex: 9e5 });
									let s = p.elementFromPoint(6, 6);
									(l = "hitme" === (s && s.id)), p.body.removeChild(i);
								}
								if (l)
									return j(
										b.location.href.split("#")[0],
										[/<[^>]*>/g, ""],
										[/([\('\)])/g, "\\$1"],
										[/ /g, "%20"]
									);
							}
							return "";
						}
						getStyle(t) {
							return (
								(this.style = T(
									{
										fontFamily: "Helvetica, Arial, sans-serif",
										fontSize: "1rem",
									},
									t
								)),
								this.style
							);
						}
						setStyle(t) {
							this.boxWrapper.css(this.getStyle(t));
						}
						isHidden() {
							return !this.boxWrapper.getBBox().width;
						}
						destroy() {
							let t = this.defs;
							return (
								(this.box = null),
								(this.boxWrapper = this.boxWrapper.destroy()),
								w(this.gradients || {}),
								(this.gradients = null),
								(this.defs = t.destroy()),
								this.unSubPixelFix && this.unSubPixelFix(),
								(this.alignedObjects = null),
								null
							);
						}
						createElement(t) {
							return new this.Element(this, t);
						}
						getRadialAttr(t, e) {
							return {
								cx: t[0] - t[2] / 2 + (e.cx || 0) * t[2],
								cy: t[1] - t[2] / 2 + (e.cy || 0) * t[2],
								r: (e.r || 0) * t[2],
							};
						}
						shadowDefinition(t) {
							let e = [
									`highcharts-drop-shadow-${this.chartIndex}`,
									...Object.keys(t).map((e) => `${e}-${t[e]}`),
								]
									.join("-")
									.toLowerCase()
									.replace(/[^a-z0-9\-]/g, ""),
								i = D(
									{
										color: "#000000",
										offsetX: 1,
										offsetY: 1,
										opacity: 0.15,
										width: 5,
									},
									t
								);
							return (
								this.defs.element.querySelector(`#${e}`) ||
									this.definition({
										tagName: "filter",
										attributes: { id: e, filterUnits: i.filterUnits },
										children: [
											{
												tagName: "feDropShadow",
												attributes: {
													dx: i.offsetX,
													dy: i.offsetY,
													"flood-color": i.color,
													"flood-opacity": Math.min(5 * i.opacity, 1),
													stdDeviation: i.width / 2,
												},
											},
										],
									}),
								e
							);
						}
						buildText(t) {
							new n(t).buildSVG();
						}
						getContrast(t) {
							let i = e.parse(t).rgba.map((t) => {
									let e = t / 255;
									return e <= 0.03928
										? e / 12.92
										: Math.pow((e + 0.055) / 1.055, 2.4);
								}),
								s = 0.2126 * i[0] + 0.7152 * i[1] + 0.0722 * i[2];
							return 1.05 / (s + 0.05) > (s + 0.05) / 0.05
								? "#FFFFFF"
								: "#000000";
						}
						button(e, i, s, r, o = {}, a, n, h, l, d) {
							let c, p, u;
							let f = this.label(
									e,
									i,
									s,
									l,
									void 0,
									void 0,
									d,
									void 0,
									"button"
								),
								m = this.styledMode,
								x = o.states || {},
								y = 0;
							(o = D(o)), delete o.states;
							let b = D(
								{
									color: "#333333",
									cursor: "pointer",
									fontSize: "0.8em",
									fontWeight: "normal",
								},
								o.style
							);
							delete o.style;
							let S = t.filterUserAttributes(o);
							return (
								f.attr(D({ padding: 8, r: 2 }, S)),
								m ||
									((S = D(
										{ fill: "#f7f7f7", stroke: "#cccccc", "stroke-width": 1 },
										S
									)),
									(c = (a = D(
										S,
										{ fill: "#e6e6e6" },
										t.filterUserAttributes(a || x.hover || {})
									)).style),
									delete a.style,
									(p = (n = D(
										S,
										{
											fill: "#e6e9ff",
											style: { color: "#000000", fontWeight: "bold" },
										},
										t.filterUserAttributes(n || x.select || {})
									)).style),
									delete n.style,
									(u = (h = D(
										S,
										{ style: { color: "#cccccc" } },
										t.filterUserAttributes(h || x.disabled || {})
									)).style),
									delete h.style),
								v(f.element, g ? "mouseover" : "mouseenter", function () {
									3 !== y && f.setState(1);
								}),
								v(f.element, g ? "mouseout" : "mouseleave", function () {
									3 !== y && f.setState(y);
								}),
								(f.setState = function (t) {
									if (
										(1 !== t && (f.state = y = t),
										f
											.removeClass(
												/highcharts-button-(normal|hover|pressed|disabled)/
											)
											.addClass(
												"highcharts-button-" +
													["normal", "hover", "pressed", "disabled"][t || 0]
											),
										!m)
									) {
										f.attr([S, a, n, h][t || 0]);
										let e = [b, c, p, u][t || 0];
										L(e) && f.css(e);
									}
								}),
								!m &&
									(f.attr(S).css(T({ cursor: "default" }, b)),
									d && f.text.css({ pointerEvents: "none" })),
								f
									.on("touchstart", (t) => t.stopPropagation())
									.on("click", function (t) {
										3 !== y && r.call(f, t);
									})
							);
						}
						crispLine(t, e, i = "round") {
							let s = t[0],
								r = t[1];
							return (
								M(s[1]) &&
									s[1] === r[1] &&
									(s[1] = r[1] = Math[i](s[1]) - (e % 2) / 2),
								M(s[2]) &&
									s[2] === r[2] &&
									(s[2] = r[2] = Math[i](s[2]) + (e % 2) / 2),
								t
							);
						}
						path(t) {
							let e = this.styledMode ? {} : { fill: "none" };
							return (
								A(t) ? (e.d = t) : L(t) && T(e, t),
								this.createElement("path").attr(e)
							);
						}
						circle(t, e, i) {
							let s = L(t) ? t : void 0 === t ? {} : { x: t, y: e, r: i },
								r = this.createElement("circle");
							return (
								(r.xSetter = r.ySetter =
									function (t, e, i) {
										i.setAttribute("c" + e, t);
									}),
								r.attr(s)
							);
						}
						arc(t, e, i, s, r, o) {
							let a;
							L(t)
								? ((e = (a = t).y),
								  (i = a.r),
								  (s = a.innerR),
								  (r = a.start),
								  (o = a.end),
								  (t = a.x))
								: (a = { innerR: s, start: r, end: o });
							let n = this.symbol("arc", t, e, i, i, a);
							return (n.r = i), n;
						}
						rect(t, e, i, s, r, o) {
							let a = L(t)
									? t
									: void 0 === t
									? {}
									: {
											x: t,
											y: e,
											r,
											width: Math.max(i || 0, 0),
											height: Math.max(s || 0, 0),
									  },
								n = this.createElement("rect");
							return (
								this.styledMode ||
									(void 0 !== o && ((a["stroke-width"] = o), T(a, n.crisp(a))),
									(a.fill = "none")),
								(n.rSetter = function (t, e, i) {
									(n.r = t), S(i, { rx: t, ry: t });
								}),
								(n.rGetter = function () {
									return n.r || 0;
								}),
								n.attr(a)
							);
						}
						roundedRect(t) {
							return this.symbol("roundedRect").attr(t);
						}
						setSize(t, e, i) {
							(this.width = t),
								(this.height = e),
								this.boxWrapper.animate(
									{ width: t, height: e },
									{
										step: function () {
											this.attr({
												viewBox:
													"0 0 " +
													this.attr("width") +
													" " +
													this.attr("height"),
											});
										},
										duration: E(i, !0) ? void 0 : 0,
									}
								),
								this.alignElements();
						}
						g(t) {
							let e = this.createElement("g");
							return t ? e.attr({ class: "highcharts-" + t }) : e;
						}
						image(t, e, i, s, r, o) {
							let a = { preserveAspectRatio: "none" };
							P(e) && (a.x = e),
								P(i) && (a.y = i),
								P(s) && (a.width = s),
								P(r) && (a.height = r);
							let n = this.createElement("image").attr(a),
								h = function (e) {
									n.attr({ href: t }), o.call(n, e);
								};
							if (o) {
								n.attr({
									href: "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==",
								});
								let e = new b.Image();
								v(e, "load", h), (e.src = t), e.complete && h({});
							} else n.attr({ href: t });
							return n;
						}
						symbol(t, e, i, s, r, o) {
							let a, n, h, l;
							let c = this,
								u = /^url\((.*?)\)$/,
								g = u.test(t),
								f = !g && (this.symbols[t] ? t : "circle"),
								m = f && this.symbols[f];
							if (m)
								"number" == typeof e &&
									(n = m.call(
										this.symbols,
										Math.round(e || 0),
										Math.round(i || 0),
										s || 0,
										r || 0,
										o
									)),
									(a = this.path(n)),
									c.styledMode || a.attr("fill", "none"),
									T(a, {
										symbolName: f || void 0,
										x: e,
										y: i,
										width: s,
										height: r,
									}),
									o && T(a, o);
							else if (g) {
								h = t.match(u)[1];
								let s = (a = this.image(h));
								(s.imgwidth = E(o && o.width, y[h] && y[h].width)),
									(s.imgheight = E(o && o.height, y[h] && y[h].height)),
									(l = (t) => t.attr({ width: t.width, height: t.height })),
									["width", "height"].forEach((t) => {
										s[`${t}Setter`] = function (t, e) {
											this[e] = t;
											let {
													alignByTranslate: i,
													element: s,
													width: r,
													height: a,
													imgwidth: n,
													imgheight: h,
												} = this,
												l = "width" === e ? n : h,
												d = 1;
											o && "within" === o.backgroundSize && r && a && n && h
												? ((d = Math.min(r / n, a / h)),
												  S(s, {
														width: Math.round(n * d),
														height: Math.round(h * d),
												  }))
												: s && l && s.setAttribute(e, l),
												!i &&
													n &&
													h &&
													this.translate(
														((r || 0) - n * d) / 2,
														((a || 0) - h * d) / 2
													);
										};
									}),
									M(e) && s.attr({ x: e, y: i }),
									(s.isImg = !0),
									M(s.imgwidth) && M(s.imgheight)
										? l(s)
										: (s.attr({ width: 0, height: 0 }),
										  k("img", {
												onload: function () {
													let t = d[c.chartIndex];
													0 === this.width &&
														(C(this, { position: "absolute", top: "-999em" }),
														p.body.appendChild(this)),
														(y[h] = { width: this.width, height: this.height }),
														(s.imgwidth = this.width),
														(s.imgheight = this.height),
														s.element && l(s),
														this.parentNode &&
															this.parentNode.removeChild(this),
														c.imgCount--,
														c.imgCount || !t || t.hasLoaded || t.onload();
												},
												src: h,
										  }),
										  this.imgCount++);
							}
							return a;
						}
						clipRect(t, e, i, s) {
							return this.rect(t, e, i, s, 0);
						}
						text(t, e, i, s) {
							let r = {};
							if (s && (this.allowHTML || !this.forExport))
								return this.html(t, e, i);
							(r.x = Math.round(e || 0)),
								i && (r.y = Math.round(i)),
								M(t) && (r.text = t);
							let o = this.createElement("text").attr(r);
							return (
								(s && (!this.forExport || this.allowHTML)) ||
									(o.xSetter = function (t, e, i) {
										let s = i.getElementsByTagName("tspan"),
											r = i.getAttribute(e);
										for (let i = 0, o; i < s.length; i++)
											(o = s[i]).getAttribute(e) === r && o.setAttribute(e, t);
										i.setAttribute(e, t);
									}),
								o
							);
						}
						fontMetrics(t) {
							let e = I(r.prototype.getStyle.call(t, "font-size") || 0),
								i = e < 24 ? e + 3 : Math.round(1.2 * e),
								s = Math.round(0.8 * i);
							return { h: i, b: s, f: e };
						}
						rotCorr(t, e, i) {
							let s = t;
							return (
								e && i && (s = Math.max(s * Math.cos(e * c), 4)),
								{ x: (-t / 3) * Math.sin(e * c), y: s }
							);
						}
						pathToSegments(t) {
							let e = [],
								i = [],
								s = { A: 8, C: 7, H: 2, L: 3, M: 3, Q: 5, S: 5, T: 3, V: 2 };
							for (let r = 0; r < t.length; r++)
								O(i[0]) &&
									P(t[r]) &&
									i.length === s[i[0].toUpperCase()] &&
									t.splice(r, 0, i[0].replace("M", "L").replace("m", "l")),
									"string" == typeof t[r] &&
										(i.length && e.push(i.slice(0)), (i.length = 0)),
									i.push(t[r]);
							return e.push(i.slice(0)), e;
						}
						label(t, e, i, s, r, a, n, h, l) {
							return new o(this, t, e, i, s, r, a, n, h, l);
						}
						alignElements() {
							this.alignedObjects.forEach((t) => t.align());
						}
					}
					return (
						T(R.prototype, {
							Element: r,
							SVG_NS: x,
							escapes: {
								"&": "&amp;",
								"<": "&lt;",
								">": "&gt;",
								"'": "&#39;",
								'"': "&quot;",
							},
							symbols: a,
							draw: m,
						}),
						s.registerRendererType("svg", R, !0),
						R
					);
				}
			),
			i(
				e,
				"Core/Renderer/HTML/HTMLElement.js",
				[
					e["Core/Renderer/HTML/AST.js"],
					e["Core/Globals.js"],
					e["Core/Renderer/SVG/SVGElement.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s) {
					let { composed: r } = e,
						{
							attr: o,
							css: a,
							createElement: n,
							defined: h,
							extend: l,
							pInt: d,
							pushUnique: c,
						} = s;
					function p(t, e, s) {
						let r = this.div?.style || s.style;
						i.prototype[`${e}Setter`].call(this, t, e, s), r && (r[e] = t);
					}
					let u = (t, e) => {
						if (!t.div) {
							let s = o(t.element, "class"),
								r = t.css,
								a = n(
									"div",
									s ? { className: s } : void 0,
									{
										position: "absolute",
										left: `${t.translateX || 0}px`,
										top: `${t.translateY || 0}px`,
										...t.styles,
										display: t.display,
										opacity: t.opacity,
										visibility: t.visibility,
									},
									t.parentGroup?.div || e
								);
							(t.classSetter = (t, e, i) => {
								i.setAttribute("class", t), (a.className = t);
							}),
								(t.translateXSetter = t.translateYSetter =
									(e, i) => {
										(t[i] = e),
											(a.style["translateX" === i ? "left" : "top"] = `${e}px`),
											(t.doTransform = !0);
									}),
								(t.opacitySetter = t.visibilitySetter = p),
								(t.css = (e) => (
									r.call(t, e),
									e.cursor && (a.style.cursor = e.cursor),
									e.pointerEvents && (a.style.pointerEvents = e.pointerEvents),
									t
								)),
								(t.on = function () {
									return (
										i.prototype.on.apply(
											{ element: a, onEvents: t.onEvents },
											arguments
										),
										t
									);
								}),
								(t.div = a);
						}
						return t.div;
					};
					class g extends i {
						static compose(t) {
							c(r, this.compose) &&
								(t.prototype.html = function (t, e, i) {
									return new g(this, "span").attr({
										text: t,
										x: Math.round(e),
										y: Math.round(i),
									});
								});
						}
						constructor(t, e) {
							super(t, e),
								this.css({
									position: "absolute",
									...(t.styledMode
										? {}
										: {
												fontFamily: t.style.fontFamily,
												fontSize: t.style.fontSize,
										  }),
								}),
								(this.element.style.whiteSpace = "nowrap");
						}
						getSpanCorrection(t, e, i) {
							(this.xCorr = -t * i), (this.yCorr = -e);
						}
						css(t) {
							let e;
							let { element: i } = this,
								s = "SPAN" === i.tagName && t && "width" in t,
								r = s && t.width;
							return (
								s &&
									(delete t.width, (this.textWidth = d(r) || void 0), (e = !0)),
								t?.textOverflow === "ellipsis" &&
									((t.whiteSpace = "nowrap"), (t.overflow = "hidden")),
								l(this.styles, t),
								a(i, t),
								e && this.updateTransform(),
								this
							);
						}
						htmlGetBBox() {
							let { element: t } = this;
							return {
								x: t.offsetLeft,
								y: t.offsetTop,
								width: t.offsetWidth,
								height: t.offsetHeight,
							};
						}
						updateTransform() {
							if (!this.added) {
								this.alignOnAdd = !0;
								return;
							}
							let {
									element: t,
									renderer: e,
									rotation: i,
									rotationOriginX: s,
									rotationOriginY: r,
									styles: o,
									textAlign: n = "left",
									textWidth: l,
									translateX: d = 0,
									translateY: c = 0,
									x: p = 0,
									y: u = 0,
								} = this,
								g = o.whiteSpace;
							if (
								(a(t, { marginLeft: `${d}px`, marginTop: `${c}px` }),
								"SPAN" === t.tagName)
							) {
								let o = [i, n, t.innerHTML, l, this.textAlign].join(","),
									d = -(this.parentGroup?.padding * 1) || 0,
									c,
									f = !1;
								if (l !== this.oldTextWidth) {
									let e = this.textPxLength
											? this.textPxLength
											: (a(t, { width: "", whiteSpace: g || "nowrap" }),
											  t.offsetWidth),
										s = l || 0;
									(s > this.oldTextWidth || e > s) &&
										(/[ \-]/.test(t.textContent || t.innerText) ||
											"ellipsis" === t.style.textOverflow) &&
										(a(t, {
											width: e > s || i ? l + "px" : "auto",
											display: "block",
											whiteSpace: g || "normal",
										}),
										(this.oldTextWidth = l),
										(f = !0));
								}
								(this.hasBoxWidthChanged = f),
									o !== this.cTT &&
										((c = e.fontMetrics(t).b),
										h(i) &&
											(i !== (this.oldRotation || 0) || n !== this.oldAlign) &&
											this.setSpanRotation(i, d, d),
										this.getSpanCorrection(
											(!h(i) && this.textPxLength) || t.offsetWidth,
											c,
											{ left: 0, center: 0.5, right: 1 }[n]
										));
								let { xCorr: m = 0, yCorr: x = 0 } = this,
									y = (s ?? p) - m - p - d,
									b = (r ?? u) - x - u - d;
								a(t, {
									left: `${p + m}px`,
									top: `${u + x}px`,
									transformOrigin: `${y}px ${b}px`,
								}),
									(this.cTT = o),
									(this.oldRotation = i),
									(this.oldAlign = n);
							}
						}
						setSpanRotation(t, e, i) {
							a(this.element, {
								transform: `rotate(${t}deg)`,
								transformOrigin: `${e}% ${i}px`,
							});
						}
						add(t) {
							let e;
							let i = this.renderer.box.parentNode,
								s = [];
							if (((this.parentGroup = t), t && !(e = t.div))) {
								let r = t;
								for (; r; ) s.push(r), (r = r.parentGroup);
								for (let t of s.reverse()) e = u(t, i);
							}
							return (
								(e || i).appendChild(this.element),
								(this.added = !0),
								this.alignOnAdd && this.updateTransform(),
								this
							);
						}
						textSetter(e) {
							e !== this.textStr &&
								(delete this.bBox,
								delete this.oldTextWidth,
								t.setElementHTML(this.element, e ?? ""),
								(this.textStr = e),
								(this.doTransform = !0));
						}
						alignSetter(t) {
							(this.alignValue = this.textAlign = t), (this.doTransform = !0);
						}
						xSetter(t, e) {
							(this[e] = t), (this.doTransform = !0);
						}
					}
					let f = g.prototype;
					return (
						(f.visibilitySetter = f.opacitySetter = p),
						(f.ySetter =
							f.rotationSetter =
							f.rotationOriginXSetter =
							f.rotationOriginYSetter =
								f.xSetter),
						g
					);
				}
			),
			i(e, "Core/Axis/AxisDefaults.js", [], function () {
				var t, e;
				return (
					((e = t || (t = {})).xAxis = {
						alignTicks: !0,
						allowDecimals: void 0,
						panningEnabled: !0,
						zIndex: 2,
						zoomEnabled: !0,
						dateTimeLabelFormats: {
							millisecond: { main: "%H:%M:%S.%L", range: !1 },
							second: { main: "%H:%M:%S", range: !1 },
							minute: { main: "%H:%M", range: !1 },
							hour: { main: "%H:%M", range: !1 },
							day: { main: "%e %b" },
							week: { main: "%e %b" },
							month: { main: "%b '%y" },
							year: { main: "%Y" },
						},
						endOnTick: !1,
						gridLineDashStyle: "Solid",
						gridZIndex: 1,
						labels: {
							autoRotationLimit: 80,
							distance: 15,
							enabled: !0,
							indentation: 10,
							overflow: "justify",
							padding: 5,
							reserveSpace: void 0,
							rotation: void 0,
							staggerLines: 0,
							step: 0,
							useHTML: !1,
							zIndex: 7,
							style: { color: "#333333", cursor: "default", fontSize: "0.8em" },
						},
						maxPadding: 0.01,
						minorGridLineDashStyle: "Solid",
						minorTickLength: 2,
						minorTickPosition: "outside",
						minorTicksPerMajor: 5,
						minPadding: 0.01,
						offset: void 0,
						reversed: void 0,
						reversedStacks: !1,
						showEmpty: !0,
						showFirstLabel: !0,
						showLastLabel: !0,
						startOfWeek: 1,
						startOnTick: !1,
						tickLength: 10,
						tickPixelInterval: 100,
						tickmarkPlacement: "between",
						tickPosition: "outside",
						title: {
							align: "middle",
							useHTML: !1,
							x: 0,
							y: 0,
							style: { color: "#666666", fontSize: "0.8em" },
						},
						type: "linear",
						uniqueNames: !0,
						visible: !0,
						minorGridLineColor: "#f2f2f2",
						minorGridLineWidth: 1,
						minorTickColor: "#999999",
						lineColor: "#333333",
						lineWidth: 1,
						gridLineColor: "#e6e6e6",
						gridLineWidth: void 0,
						tickColor: "#333333",
					}),
					(e.yAxis = {
						reversedStacks: !0,
						endOnTick: !0,
						maxPadding: 0.05,
						minPadding: 0.05,
						tickPixelInterval: 72,
						showLastLabel: !0,
						labels: { x: void 0 },
						startOnTick: !0,
						title: { text: "Values" },
						stackLabels: {
							animation: {},
							allowOverlap: !1,
							enabled: !1,
							crop: !0,
							overflow: "justify",
							formatter: function () {
								let { numberFormatter: t } = this.axis.chart;
								return t(this.total || 0, -1);
							},
							style: {
								color: "#000000",
								fontSize: "0.7em",
								fontWeight: "bold",
								textOutline: "1px contrast",
							},
						},
						gridLineWidth: 1,
						lineWidth: 0,
					}),
					t
				);
			}),
			i(e, "Core/Foundation.js", [e["Core/Utilities.js"]], function (t) {
				var e;
				let { addEvent: i, isFunction: s, objectEach: r, removeEvent: o } = t;
				return (
					((e || (e = {})).registerEventOptions = function (t, e) {
						(t.eventOptions = t.eventOptions || {}),
							r(e.events, function (e, r) {
								t.eventOptions[r] !== e &&
									(t.eventOptions[r] &&
										(o(t, r, t.eventOptions[r]), delete t.eventOptions[r]),
									s(e) && ((t.eventOptions[r] = e), i(t, r, e, { order: 0 })));
							});
					}),
					e
				);
			}),
			i(
				e,
				"Core/Axis/Tick.js",
				[e["Core/Templating.js"], e["Core/Globals.js"], e["Core/Utilities.js"]],
				function (t, e, i) {
					let { deg2rad: s } = e,
						{
							clamp: r,
							correctFloat: o,
							defined: a,
							destroyObjectProperties: n,
							extend: h,
							fireEvent: l,
							isNumber: d,
							merge: c,
							objectEach: p,
							pick: u,
						} = i;
					return class {
						constructor(t, e, i, s, r) {
							(this.isNew = !0),
								(this.isNewLabel = !0),
								(this.axis = t),
								(this.pos = e),
								(this.type = i || ""),
								(this.parameters = r || {}),
								(this.tickmarkOffset = this.parameters.tickmarkOffset),
								(this.options = this.parameters.options),
								l(this, "init"),
								i || s || this.addLabel();
						}
						addLabel() {
							let e = this,
								i = e.axis,
								s = i.options,
								r = i.chart,
								n = i.categories,
								c = i.logarithmic,
								p = i.names,
								g = e.pos,
								f = u(e.options && e.options.labels, s.labels),
								m = i.tickPositions,
								x = g === m[0],
								y = g === m[m.length - 1],
								b = (!f.step || 1 === f.step) && 1 === i.tickInterval,
								v = m.info,
								S = e.label,
								k,
								C,
								M,
								w = this.parameters.category || (n ? u(n[g], p[g], g) : g);
							c && d(w) && (w = o(c.lin2log(w))),
								i.dateTime &&
									(v
										? (k = (C = r.time.resolveDTLFormat(
												s.dateTimeLabelFormats[
													(!s.grid && v.higherRanks[g]) || v.unitName
												]
										  )).main)
										: d(w) &&
										  (k = i.dateTime.getXDateFormat(
												w,
												s.dateTimeLabelFormats || {}
										  ))),
								(e.isFirst = x),
								(e.isLast = y);
							let T = {
								axis: i,
								chart: r,
								dateTimeLabelFormat: k,
								isFirst: x,
								isLast: y,
								pos: g,
								tick: e,
								tickPositionInfo: v,
								value: w,
							};
							l(this, "labelFormat", T);
							let A = (e) =>
									f.formatter
										? f.formatter.call(e, e)
										: f.format
										? ((e.text = i.defaultLabelFormatter.call(e)),
										  t.format(f.format, e, r))
										: i.defaultLabelFormatter.call(e),
								P = A.call(T, T),
								L = C && C.list;
							L
								? (e.shortenLabel = function () {
										for (M = 0; M < L.length; M++)
											if (
												(h(T, { dateTimeLabelFormat: L[M] }),
												S.attr({ text: A.call(T, T) }),
												S.getBBox().width < i.getSlotWidth(e) - 2 * f.padding)
											)
												return;
										S.attr({ text: "" });
								  })
								: (e.shortenLabel = void 0),
								b && i._addedPlotLB && e.moveLabel(P, f),
								a(S) || e.movedLabel
									? S &&
									  S.textStr !== P &&
									  !b &&
									  (!S.textWidth ||
											f.style.width ||
											S.styles.width ||
											S.css({ width: null }),
									  S.attr({ text: P }),
									  (S.textPxLength = S.getBBox().width))
									: ((e.label = S = e.createLabel(P, f)), (e.rotation = 0));
						}
						createLabel(t, e, i) {
							let s = this.axis,
								r = s.chart,
								o =
									a(t) && e.enabled
										? r.renderer
												.text(t, i?.x, i?.y, e.useHTML)
												.add(s.labelGroup)
										: void 0;
							return (
								o &&
									(r.styledMode || o.css(c(e.style)),
									(o.textPxLength = o.getBBox().width)),
								o
							);
						}
						destroy() {
							n(this, this.axis);
						}
						getPosition(t, e, i, s) {
							let a = this.axis,
								n = a.chart,
								h = (s && n.oldChartHeight) || n.chartHeight,
								d = {
									x: t
										? o(a.translate(e + i, void 0, void 0, s) + a.transB)
										: a.left +
										  a.offset +
										  (a.opposite
												? ((s && n.oldChartWidth) || n.chartWidth) -
												  a.right -
												  a.left
												: 0),
									y: t
										? h - a.bottom + a.offset - (a.opposite ? a.height : 0)
										: o(h - a.translate(e + i, void 0, void 0, s) - a.transB),
								};
							return (
								(d.y = r(d.y, -1e5, 1e5)),
								l(this, "afterGetPosition", { pos: d }),
								d
							);
						}
						getLabelPosition(t, e, i, r, o, n, h, d) {
							let c, p;
							let g = this.axis,
								f = g.transA,
								m =
									g.isLinked && g.linkedParent
										? g.linkedParent.reversed
										: g.reversed,
								x = g.staggerLines,
								y = g.tickRotCorr || { x: 0, y: 0 },
								b =
									r || g.reserveSpaceDefault
										? 0
										: -g.labelOffset * ("center" === g.labelAlign ? 0.5 : 1),
								v = o.distance,
								S = {};
							return (
								(c =
									0 === g.side
										? i.rotation
											? -v
											: -i.getBBox().height
										: 2 === g.side
										? y.y + v
										: Math.cos(i.rotation * s) *
										  (y.y - i.getBBox(!1, 0).height / 2)),
								a(o.y) && (c = 0 === g.side && g.horiz ? o.y + c : o.y),
								(t =
									t +
									u(o.x, [0, 1, 0, -1][g.side] * v) +
									b +
									y.x -
									(n && r ? n * f * (m ? -1 : 1) : 0)),
								(e = e + c - (n && !r ? n * f * (m ? 1 : -1) : 0)),
								x &&
									((p = (h / (d || 1)) % x),
									g.opposite && (p = x - p - 1),
									(e += (g.labelOffset / x) * p)),
								(S.x = t),
								(S.y = Math.round(e)),
								l(this, "afterGetLabelPosition", {
									pos: S,
									tickmarkOffset: n,
									index: h,
								}),
								S
							);
						}
						getLabelSize() {
							return this.label
								? this.label.getBBox()[this.axis.horiz ? "height" : "width"]
								: 0;
						}
						getMarkPath(t, e, i, s, r, o) {
							return o.crispLine(
								[
									["M", t, e],
									["L", t + (r ? 0 : -i), e + (r ? i : 0)],
								],
								s
							);
						}
						handleOverflow(t) {
							let e = this.axis,
								i = e.options.labels,
								r = t.x,
								o = e.chart.chartWidth,
								a = e.chart.spacing,
								n = u(e.labelLeft, Math.min(e.pos, a[3])),
								h = u(
									e.labelRight,
									Math.max(e.isRadial ? 0 : e.pos + e.len, o - a[1])
								),
								l = this.label,
								d = this.rotation,
								c = { left: 0, center: 0.5, right: 1 }[
									e.labelAlign || l.attr("align")
								],
								p = l.getBBox().width,
								g = e.getSlotWidth(this),
								f = {},
								m = g,
								x = 1,
								y,
								b,
								v;
							d || "justify" !== i.overflow
								? d < 0 && r - c * p < n
									? (v = Math.round(r / Math.cos(d * s) - n))
									: d > 0 &&
									  r + c * p > h &&
									  (v = Math.round((o - r) / Math.cos(d * s)))
								: ((y = r - c * p),
								  (b = r + (1 - c) * p),
								  y < n
										? (m = t.x + m * (1 - c) - n)
										: b > h && ((m = h - t.x + m * c), (x = -1)),
								  (m = Math.min(g, m)) < g &&
										"center" === e.labelAlign &&
										(t.x += x * (g - m - c * (g - Math.min(p, m)))),
								  (p > m || (e.autoRotation && (l.styles || {}).width)) &&
										(v = m)),
								v &&
									(this.shortenLabel
										? this.shortenLabel()
										: ((f.width = Math.floor(v) + "px"),
										  (i.style || {}).textOverflow ||
												(f.textOverflow = "ellipsis"),
										  l.css(f)));
						}
						moveLabel(t, e) {
							let i = this,
								s = i.label,
								r = i.axis,
								o = !1,
								a;
							s && s.textStr === t
								? ((i.movedLabel = s), (o = !0), delete i.label)
								: p(r.ticks, function (e) {
										o ||
											e.isNew ||
											e === i ||
											!e.label ||
											e.label.textStr !== t ||
											((i.movedLabel = e.label),
											(o = !0),
											(e.labelPos = i.movedLabel.xy),
											delete e.label);
								  }),
								!o &&
									(i.labelPos || s) &&
									((a = i.labelPos || s.xy),
									(i.movedLabel = i.createLabel(t, e, a)),
									i.movedLabel && i.movedLabel.attr({ opacity: 0 }));
						}
						render(t, e, i) {
							let s = this.axis,
								r = s.horiz,
								a = this.pos,
								n = u(this.tickmarkOffset, s.tickmarkOffset),
								h = this.getPosition(r, a, n, e),
								d = h.x,
								c = h.y,
								p = s.pos,
								g = p + s.len,
								f = (r && d === g) || (!r && c === p) ? -1 : 1,
								m = r ? d : c;
							!s.chart.polar && this.isNew && (o(m) < p || m > g) && (i = 0);
							let x = u(i, this.label && this.label.newOpacity, 1);
							(i = u(i, 1)),
								(this.isActive = !0),
								this.renderGridLine(e, i, f),
								this.renderMark(h, i, f),
								this.renderLabel(h, e, x, t),
								(this.isNew = !1),
								l(this, "afterRender");
						}
						renderGridLine(t, e, i) {
							let s = this.axis,
								r = s.options,
								o = {},
								a = this.pos,
								n = this.type,
								h = u(this.tickmarkOffset, s.tickmarkOffset),
								l = s.chart.renderer,
								d = this.gridLine,
								c,
								p = r.gridLineWidth,
								g = r.gridLineColor,
								f = r.gridLineDashStyle;
							"minor" === this.type &&
								((p = r.minorGridLineWidth),
								(g = r.minorGridLineColor),
								(f = r.minorGridLineDashStyle)),
								d ||
									(s.chart.styledMode ||
										((o.stroke = g),
										(o["stroke-width"] = p || 0),
										(o.dashstyle = f)),
									n || (o.zIndex = 1),
									t && (e = 0),
									(this.gridLine = d =
										l
											.path()
											.attr(o)
											.addClass(
												"highcharts-" + (n ? n + "-" : "") + "grid-line"
											)
											.add(s.gridGroup))),
								d &&
									(c = s.getPlotLinePath({
										value: a + h,
										lineWidth: d.strokeWidth() * i,
										force: "pass",
										old: t,
										acrossPanes: !1,
									})) &&
									d[t || this.isNew ? "attr" : "animate"]({ d: c, opacity: e });
						}
						renderMark(t, e, i) {
							let s = this.axis,
								r = s.options,
								o = s.chart.renderer,
								a = this.type,
								n = s.tickSize(a ? a + "Tick" : "tick"),
								h = t.x,
								l = t.y,
								d = u(
									r["minor" !== a ? "tickWidth" : "minorTickWidth"],
									!a && s.isXAxis ? 1 : 0
								),
								c = r["minor" !== a ? "tickColor" : "minorTickColor"],
								p = this.mark,
								g = !p;
							n &&
								(s.opposite && (n[0] = -n[0]),
								p ||
									((this.mark = p =
										o
											.path()
											.addClass("highcharts-" + (a ? a + "-" : "") + "tick")
											.add(s.axisGroup)),
									s.chart.styledMode ||
										p.attr({ stroke: c, "stroke-width": d })),
								p[g ? "attr" : "animate"]({
									d: this.getMarkPath(
										h,
										l,
										n[0],
										p.strokeWidth() * i,
										s.horiz,
										o
									),
									opacity: e,
								}));
						}
						renderLabel(t, e, i, s) {
							let r = this.axis,
								o = r.horiz,
								a = r.options,
								n = this.label,
								h = a.labels,
								l = h.step,
								c = u(this.tickmarkOffset, r.tickmarkOffset),
								p = t.x,
								g = t.y,
								f = !0;
							n &&
								d(p) &&
								((n.xy = t = this.getLabelPosition(p, g, n, o, h, c, s, l)),
								(!this.isFirst || this.isLast || a.showFirstLabel) &&
								(!this.isLast || this.isFirst || a.showLastLabel)
									? !o ||
									  h.step ||
									  h.rotation ||
									  e ||
									  0 === i ||
									  this.handleOverflow(t)
									: (f = !1),
								l && s % l && (f = !1),
								f && d(t.y)
									? ((t.opacity = i),
									  n[this.isNewLabel ? "attr" : "animate"](t).show(!0),
									  (this.isNewLabel = !1))
									: (n.hide(), (this.isNewLabel = !0)));
						}
						replaceMovedLabel() {
							let t = this.label,
								e = this.axis;
							t &&
								!this.isNew &&
								(t.animate({ opacity: 0 }, void 0, t.destroy),
								delete this.label),
								(e.isDirty = !0),
								(this.label = this.movedLabel),
								delete this.movedLabel;
						}
					};
				}
			),
			i(
				e,
				"Core/Axis/Axis.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Axis/AxisDefaults.js"],
					e["Core/Color/Color.js"],
					e["Core/Defaults.js"],
					e["Core/Foundation.js"],
					e["Core/Globals.js"],
					e["Core/Axis/Tick.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r, o, a, n) {
					let { animObject: h } = t,
						{ xAxis: l, yAxis: d } = e,
						{ defaultOptions: c } = s,
						{ registerEventOptions: p } = r,
						{ deg2rad: u } = o,
						{
							arrayMax: g,
							arrayMin: f,
							clamp: m,
							correctFloat: x,
							defined: y,
							destroyObjectProperties: b,
							erase: v,
							error: S,
							extend: k,
							fireEvent: C,
							getClosestDistance: M,
							insertItem: w,
							isArray: T,
							isNumber: A,
							isString: P,
							merge: L,
							normalizeTickInterval: O,
							objectEach: D,
							pick: E,
							relativeLength: I,
							removeEvent: j,
							splat: B,
							syncTimeout: R,
						} = n,
						z = (t, e) =>
							O(
								e,
								void 0,
								void 0,
								E(t.options.allowDecimals, e < 0.5 || void 0 !== t.tickAmount),
								!!t.tickAmount
							);
					k(c, { xAxis: l, yAxis: L(l, d) });
					class N {
						constructor(t, e, i) {
							this.init(t, e, i);
						}
						init(t, e, i = this.coll) {
							let s = "xAxis" === i,
								r = this.isZAxis || (t.inverted ? !s : s);
							(this.chart = t),
								(this.horiz = r),
								(this.isXAxis = s),
								(this.coll = i),
								C(this, "init", { userOptions: e }),
								(this.opposite = E(e.opposite, this.opposite)),
								(this.side = E(
									e.side,
									this.side,
									r ? (this.opposite ? 0 : 2) : this.opposite ? 1 : 3
								)),
								this.setOptions(e);
							let o = this.options,
								a = o.labels,
								n = o.type;
							(this.userOptions = e),
								(this.minPixelPadding = 0),
								(this.reversed = E(o.reversed, this.reversed)),
								(this.visible = o.visible),
								(this.zoomEnabled = o.zoomEnabled),
								(this.hasNames = "category" === n || !0 === o.categories),
								(this.categories =
									(T(o.categories) && o.categories) ||
									(this.hasNames ? [] : void 0)),
								this.names || ((this.names = []), (this.names.keys = {})),
								(this.plotLinesAndBandsGroups = {}),
								(this.positiveValuesOnly = !!this.logarithmic),
								(this.isLinked = y(o.linkedTo)),
								(this.ticks = {}),
								(this.labelEdge = []),
								(this.minorTicks = {}),
								(this.plotLinesAndBands = []),
								(this.alternateBands = {}),
								(this.len = 0),
								(this.minRange = this.userMinRange = o.minRange || o.maxZoom),
								(this.range = o.range),
								(this.offset = o.offset || 0),
								(this.max = void 0),
								(this.min = void 0);
							let h = E(
								o.crosshair,
								B(t.options.tooltip.crosshairs)[s ? 0 : 1]
							);
							(this.crosshair = !0 === h ? {} : h),
								-1 === t.axes.indexOf(this) &&
									(s
										? t.axes.splice(t.xAxis.length, 0, this)
										: t.axes.push(this),
									w(this, t[this.coll])),
								t.orderItems(this.coll),
								(this.series = this.series || []),
								t.inverted &&
									!this.isZAxis &&
									s &&
									!y(this.reversed) &&
									(this.reversed = !0),
								(this.labelRotation = A(a.rotation) ? a.rotation : void 0),
								p(this, o),
								C(this, "afterInit");
						}
						setOptions(t) {
							let e = this.horiz
								? { labels: { autoRotation: [-45] }, margin: 15 }
								: { title: { rotation: 90 * this.side } };
							(this.options = L(e, c[this.coll], t)),
								C(this, "afterSetOptions", { userOptions: t });
						}
						defaultLabelFormatter() {
							let t = this.axis,
								{ numberFormatter: e } = this.chart,
								i = A(this.value) ? this.value : NaN,
								s = t.chart.time,
								r = t.categories,
								o = this.dateTimeLabelFormat,
								a = c.lang,
								n = a.numericSymbols,
								h = a.numericSymbolMagnitude || 1e3,
								l = t.logarithmic ? Math.abs(i) : t.tickInterval,
								d = n && n.length,
								p,
								u;
							if (r) u = `${this.value}`;
							else if (o) u = s.dateFormat(o, i);
							else if (d && n && l >= 1e3)
								for (; d-- && void 0 === u; )
									l >= (p = Math.pow(h, d + 1)) &&
										(10 * i) % p == 0 &&
										null !== n[d] &&
										0 !== i &&
										(u = e(i / p, -1) + n[d]);
							return (
								void 0 === u &&
									(u = Math.abs(i) >= 1e4 ? e(i, -1) : e(i, -1, void 0, "")),
								u
							);
						}
						getSeriesExtremes() {
							let t;
							let e = this;
							C(this, "getSeriesExtremes", null, function () {
								(e.hasVisibleSeries = !1),
									(e.dataMin = e.dataMax = e.threshold = void 0),
									(e.softThreshold = !e.isXAxis),
									e.series.forEach((i) => {
										if (i.reserveSpace()) {
											let s = i.options,
												r,
												o = s.threshold,
												a,
												n;
											if (
												((e.hasVisibleSeries = !0),
												e.positiveValuesOnly && 0 >= (o || 0) && (o = void 0),
												e.isXAxis)
											)
												(r = i.xData) &&
													r.length &&
													((r = e.logarithmic ? r.filter((t) => t > 0) : r),
													(a = (t = i.getXExtremes(r)).min),
													(n = t.max),
													A(a) ||
														a instanceof Date ||
														((r = r.filter(A)),
														(a = (t = i.getXExtremes(r)).min),
														(n = t.max)),
													r.length &&
														((e.dataMin = Math.min(E(e.dataMin, a), a)),
														(e.dataMax = Math.max(E(e.dataMax, n), n))));
											else {
												let t = i.applyExtremes();
												A(t.dataMin) &&
													((a = t.dataMin),
													(e.dataMin = Math.min(E(e.dataMin, a), a))),
													A(t.dataMax) &&
														((n = t.dataMax),
														(e.dataMax = Math.max(E(e.dataMax, n), n))),
													y(o) && (e.threshold = o),
													(!s.softThreshold || e.positiveValuesOnly) &&
														(e.softThreshold = !1);
											}
										}
									});
							}),
								C(this, "afterGetSeriesExtremes");
						}
						translate(t, e, i, s, r, o) {
							let a = this.linkedParent || this,
								n = s && a.old ? a.old.min : a.min;
							if (!A(n)) return NaN;
							let h = a.minPixelPadding,
								l =
									(a.isOrdinal ||
										a.brokenAxis?.hasBreaks ||
										(a.logarithmic && r)) &&
									a.lin2val,
								d = 1,
								c = 0,
								p = s && a.old ? a.old.transA : a.transA,
								u = 0;
							if (
								(p || (p = a.transA),
								i && ((d *= -1), (c = a.len)),
								a.reversed && ((d *= -1), (c -= d * (a.sector || a.len))),
								e)
							)
								(u = (t = t * d + c - h) / p + n), l && (u = a.lin2val(u));
							else {
								l && (t = a.val2lin(t));
								let e = d * (t - n) * p;
								u = (a.isRadial ? e : x(e)) + c + d * h + (A(o) ? p * o : 0);
							}
							return u;
						}
						toPixels(t, e) {
							return (
								this.translate(t, !1, !this.horiz, void 0, !0) +
								(e ? 0 : this.pos)
							);
						}
						toValue(t, e) {
							return this.translate(
								t - (e ? 0 : this.pos),
								!0,
								!this.horiz,
								void 0,
								!0
							);
						}
						getPlotLinePath(t) {
							let e = this,
								i = e.chart,
								s = e.left,
								r = e.top,
								o = t.old,
								a = t.value,
								n = t.lineWidth,
								h = (o && i.oldChartHeight) || i.chartHeight,
								l = (o && i.oldChartWidth) || i.chartWidth,
								d = e.transB,
								c = t.translatedValue,
								p = t.force,
								u,
								g,
								f,
								x,
								y;
							function b(t, e, i) {
								return (
									"pass" !== p &&
										(t < e || t > i) &&
										(p ? (t = m(t, e, i)) : (y = !0)),
									t
								);
							}
							let v = {
								value: a,
								lineWidth: n,
								old: o,
								force: p,
								acrossPanes: t.acrossPanes,
								translatedValue: c,
							};
							return (
								C(this, "getPlotLinePath", v, function (t) {
									(u = f =
										Math.round(
											(c = m(
												(c = E(c, e.translate(a, void 0, void 0, o))),
												-1e5,
												1e5
											)) + d
										)),
										(g = x = Math.round(h - c - d)),
										A(c)
											? e.horiz
												? ((g = r),
												  (x = h - e.bottom + (i.scrollablePixelsY || 0)),
												  (u = f = b(u, s, s + e.width)))
												: ((u = s),
												  (f = l - e.right + (i.scrollablePixelsX || 0)),
												  (g = x = b(g, r, r + e.height)))
											: ((y = !0), (p = !1)),
										(t.path =
											y && !p
												? void 0
												: i.renderer.crispLine(
														[
															["M", u, g],
															["L", f, x],
														],
														n || 1
												  ));
								}),
								v.path
							);
						}
						getLinearTickPositions(t, e, i) {
							let s, r, o;
							let a = x(Math.floor(e / t) * t),
								n = x(Math.ceil(i / t) * t),
								h = [];
							if ((x(a + t) === a && (o = 20), this.single)) return [e];
							for (s = a; s <= n && (h.push(s), (s = x(s + t, o)) !== r); )
								r = s;
							return h;
						}
						getMinorTickInterval() {
							let { minorTicks: t, minorTickInterval: e } = this.options;
							return !0 === t ? E(e, "auto") : !1 !== t ? e : void 0;
						}
						getMinorTickPositions() {
							let t = this.options,
								e = this.tickPositions,
								i = this.minorTickInterval,
								s = this.pointRangePadding || 0,
								r = (this.min || 0) - s,
								o = (this.max || 0) + s,
								a = o - r,
								n = [],
								h;
							if (a && a / i < this.len / 3) {
								let s = this.logarithmic;
								if (s)
									this.paddedTicks.forEach(function (t, e, r) {
										e &&
											n.push.apply(
												n,
												s.getLogTickPositions(i, r[e - 1], r[e], !0)
											);
									});
								else if (
									this.dateTime &&
									"auto" === this.getMinorTickInterval()
								)
									n = n.concat(
										this.getTimeTicks(
											this.dateTime.normalizeTimeTickInterval(i),
											r,
											o,
											t.startOfWeek
										)
									);
								else
									for (h = r + ((e[0] - r) % i); h <= o && h !== n[0]; h += i)
										n.push(h);
							}
							return 0 !== n.length && this.trimTicks(n), n;
						}
						adjustForMinRange() {
							let t = this.options,
								e = this.logarithmic,
								{ max: i, min: s, minRange: r } = this,
								o,
								a,
								n,
								h;
							this.isXAxis &&
								void 0 === r &&
								!e &&
								(r =
									y(t.min) || y(t.max) || y(t.floor) || y(t.ceiling)
										? null
										: Math.min(
												5 *
													(M(
														this.series.map(
															(t) =>
																(t.xIncrement
																	? t.xData?.slice(0, 2)
																	: t.xData) || []
														)
													) || 0),
												this.dataMax - this.dataMin
										  )),
								A(i) &&
									A(s) &&
									A(r) &&
									i - s < r &&
									((a = this.dataMax - this.dataMin >= r),
									(o = (r - i + s) / 2),
									(n = [s - o, E(t.min, s - o)]),
									a && (n[2] = e ? e.log2lin(this.dataMin) : this.dataMin),
									(h = [(s = g(n)) + r, E(t.max, s + r)]),
									a && (h[2] = e ? e.log2lin(this.dataMax) : this.dataMax),
									(i = f(h)) - s < r &&
										((n[0] = i - r), (n[1] = E(t.min, i - r)), (s = g(n)))),
								(this.minRange = r),
								(this.min = s),
								(this.max = i);
						}
						getClosest() {
							let t, e;
							if (this.categories) e = 1;
							else {
								let i = [];
								this.series.forEach(function (t) {
									let s = t.closestPointRange;
									t.xData?.length === 1
										? i.push(t.xData[0])
										: !t.noSharedTooltip &&
										  y(s) &&
										  t.reserveSpace() &&
										  (e = y(e) ? Math.min(e, s) : s);
								}),
									i.length && (i.sort((t, e) => t - e), (t = M([i])));
							}
							return t && e ? Math.min(t, e) : t || e;
						}
						nameToX(t) {
							let e = T(this.options.categories),
								i = e ? this.categories : this.names,
								s = t.options.x,
								r;
							return (
								(t.series.requireSorting = !1),
								y(s) ||
									(s =
										this.options.uniqueNames && i
											? e
												? i.indexOf(t.name)
												: E(i.keys[t.name], -1)
											: t.series.autoIncrement()),
								-1 === s ? !e && i && (r = i.length) : (r = s),
								void 0 !== r
									? ((this.names[r] = t.name), (this.names.keys[t.name] = r))
									: t.x && (r = t.x),
								r
							);
						}
						updateNames() {
							let t = this,
								e = this.names;
							e.length > 0 &&
								(Object.keys(e.keys).forEach(function (t) {
									delete e.keys[t];
								}),
								(e.length = 0),
								(this.minRange = this.userMinRange),
								(this.series || []).forEach((e) => {
									(e.xIncrement = null),
										(!e.points || e.isDirtyData) &&
											((t.max = Math.max(t.max, e.xData.length - 1)),
											e.processData(),
											e.generatePoints()),
										e.data.forEach(function (i, s) {
											let r;
											i?.options &&
												void 0 !== i.name &&
												void 0 !== (r = t.nameToX(i)) &&
												r !== i.x &&
												((i.x = r), (e.xData[s] = r));
										});
								}));
						}
						setAxisTranslation() {
							let t = this,
								e = t.max - t.min,
								i = t.linkedParent,
								s = !!t.categories,
								r = t.isXAxis,
								o = t.axisPointRange || 0,
								a,
								n = 0,
								h = 0,
								l,
								d = t.transA;
							(r || s || o) &&
								((a = t.getClosest()),
								i
									? ((n = i.minPointOffset), (h = i.pointRangePadding))
									: t.series.forEach(function (e) {
											let i = s
													? 1
													: r
													? E(e.options.pointRange, a, 0)
													: t.axisPointRange || 0,
												l = e.options.pointPlacement;
											if (((o = Math.max(o, i)), !t.single || s)) {
												let t = e.is("xrange") ? !r : r;
												(n = Math.max(n, t && P(l) ? 0 : i / 2)),
													(h = Math.max(h, t && "on" === l ? 0 : i));
											}
									  }),
								(l =
									t.ordinal && t.ordinal.slope && a ? t.ordinal.slope / a : 1),
								(t.minPointOffset = n *= l),
								(t.pointRangePadding = h *= l),
								(t.pointRange = Math.min(o, t.single && s ? 1 : e)),
								r && a && (t.closestPointRange = a)),
								(t.translationSlope =
									t.transA =
									d =
										t.staticScale || t.len / (e + h || 1)),
								(t.transB = t.horiz ? t.left : t.bottom),
								(t.minPixelPadding = d * n),
								C(this, "afterSetAxisTranslation");
						}
						minFromRange() {
							let { max: t, min: e } = this;
							return (A(t) && A(e) && t - e) || void 0;
						}
						setTickInterval(t) {
							let {
									categories: e,
									chart: i,
									dataMax: s,
									dataMin: r,
									dateTime: o,
									isXAxis: a,
									logarithmic: n,
									options: h,
									softThreshold: l,
								} = this,
								d = A(this.threshold) ? this.threshold : void 0,
								c = this.minRange || 0,
								{
									ceiling: p,
									floor: u,
									linkedTo: g,
									softMax: f,
									softMin: m,
								} = h,
								b = A(g) && i[this.coll]?.[g],
								v = h.tickPixelInterval,
								k = h.maxPadding,
								M = h.minPadding,
								w = 0,
								T,
								P =
									A(h.tickInterval) && h.tickInterval >= 0
										? h.tickInterval
										: void 0,
								L,
								O,
								D,
								I;
							if (
								(o || e || b || this.getTickAmount(),
								(D = E(this.userMin, h.min)),
								(I = E(this.userMax, h.max)),
								b
									? ((this.linkedParent = b),
									  (T = b.getExtremes()),
									  (this.min = E(T.min, T.dataMin)),
									  (this.max = E(T.max, T.dataMax)),
									  h.type !== b.options.type && S(11, !0, i))
									: (l &&
											y(d) &&
											A(s) &&
											A(r) &&
											(r >= d
												? ((L = d), (M = 0))
												: s <= d && ((O = d), (k = 0))),
									  (this.min = E(D, L, r)),
									  (this.max = E(I, O, s))),
								A(this.max) &&
									A(this.min) &&
									(n &&
										(this.positiveValuesOnly &&
											!t &&
											0 >= Math.min(this.min, E(r, this.min)) &&
											S(10, !0, i),
										(this.min = x(n.log2lin(this.min), 16)),
										(this.max = x(n.log2lin(this.max), 16))),
									this.range &&
										A(r) &&
										((this.userMin =
											this.min =
											D =
												Math.max(r, this.minFromRange() || 0)),
										(this.userMax = I = this.max),
										(this.range = void 0))),
								C(this, "foundExtremes"),
								this.adjustForMinRange(),
								A(this.min) && A(this.max))
							) {
								if (
									(!A(this.userMin) &&
										A(m) &&
										m < this.min &&
										(this.min = D = m),
									!A(this.userMax) &&
										A(f) &&
										f > this.max &&
										(this.max = I = f),
									e ||
										this.axisPointRange ||
										this.stacking?.usePercentage ||
										b ||
										!(w = this.max - this.min) ||
										(!y(D) && M && (this.min -= w * M),
										y(I) || !k || (this.max += w * k)),
									!A(this.userMin) &&
										A(u) &&
										(this.min = Math.max(this.min, u)),
									!A(this.userMax) &&
										A(p) &&
										(this.max = Math.min(this.max, p)),
									l && A(r) && A(s))
								) {
									let t = d || 0;
									!y(D) && this.min < t && r >= t
										? (this.min = h.minRange ? Math.min(t, this.max - c) : t)
										: !y(I) &&
										  this.max > t &&
										  s <= t &&
										  (this.max = h.minRange ? Math.max(t, this.min + c) : t);
								}
								!i.polar &&
									this.min > this.max &&
									(y(h.min)
										? (this.max = this.min)
										: y(h.max) && (this.min = this.max)),
									(w = this.max - this.min);
							}
							if (
								(this.min !== this.max && A(this.min) && A(this.max)
									? b && !P && v === b.options.tickPixelInterval
										? (this.tickInterval = P = b.tickInterval)
										: (this.tickInterval = E(
												P,
												this.tickAmount
													? w / Math.max(this.tickAmount - 1, 1)
													: void 0,
												e ? 1 : (w * v) / Math.max(this.len, v)
										  ))
									: (this.tickInterval = 1),
								a && !t)
							) {
								let t =
									this.min !== this.old?.min || this.max !== this.old?.max;
								this.series.forEach(function (e) {
									(e.forceCrop = e.forceCropping?.()), e.processData(t);
								}),
									C(this, "postProcessData", { hasExtremesChanged: t });
							}
							this.setAxisTranslation(),
								C(this, "initialAxisTranslation"),
								this.pointRange &&
									!P &&
									(this.tickInterval = Math.max(
										this.pointRange,
										this.tickInterval
									));
							let j = E(
								h.minTickInterval,
								o && !this.series.some((t) => t.noSharedTooltip)
									? this.closestPointRange
									: 0
							);
							!P && this.tickInterval < j && (this.tickInterval = j),
								o || n || P || (this.tickInterval = z(this, this.tickInterval)),
								this.tickAmount || (this.tickInterval = this.unsquish()),
								this.setTickPositions();
						}
						setTickPositions() {
							let t = this.options,
								e = t.tickPositions,
								i = t.tickPositioner,
								s = this.getMinorTickInterval(),
								r = !this.isPanning,
								o = r && t.startOnTick,
								a = r && t.endOnTick,
								n = [],
								h;
							if (
								((this.tickmarkOffset =
									this.categories &&
									"between" === t.tickmarkPlacement &&
									1 === this.tickInterval
										? 0.5
										: 0),
								(this.minorTickInterval =
									"auto" === s && this.tickInterval
										? this.tickInterval / t.minorTicksPerMajor
										: s),
								(this.single =
									this.min === this.max &&
									y(this.min) &&
									!this.tickAmount &&
									(this.min % 1 == 0 || !1 !== t.allowDecimals)),
								e)
							)
								n = e.slice();
							else if (A(this.min) && A(this.max)) {
								if (
									!this.ordinal?.positions &&
									(this.max - this.min) / this.tickInterval >
										Math.max(2 * this.len, 200)
								)
									(n = [this.min, this.max]), S(19, !1, this.chart);
								else if (this.dateTime)
									n = this.getTimeTicks(
										this.dateTime.normalizeTimeTickInterval(
											this.tickInterval,
											t.units
										),
										this.min,
										this.max,
										t.startOfWeek,
										this.ordinal?.positions,
										this.closestPointRange,
										!0
									);
								else if (this.logarithmic)
									n = this.logarithmic.getLogTickPositions(
										this.tickInterval,
										this.min,
										this.max
									);
								else {
									let t = this.tickInterval,
										e = t;
									for (; e <= 2 * t; )
										if (
											((n = this.getLinearTickPositions(
												this.tickInterval,
												this.min,
												this.max
											)),
											this.tickAmount && n.length > this.tickAmount)
										)
											this.tickInterval = z(this, (e *= 1.1));
										else break;
								}
								n.length > this.len &&
									(n = [n[0], n[n.length - 1]])[0] === n[1] &&
									(n.length = 1),
									i &&
										((this.tickPositions = n),
										(h = i.apply(this, [this.min, this.max])) && (n = h));
							}
							(this.tickPositions = n),
								(this.paddedTicks = n.slice(0)),
								this.trimTicks(n, o, a),
								!this.isLinked &&
									A(this.min) &&
									A(this.max) &&
									(this.single &&
										n.length < 2 &&
										!this.categories &&
										!this.series.some(
											(t) =>
												t.is("heatmap") &&
												"between" === t.options.pointPlacement
										) &&
										((this.min -= 0.5), (this.max += 0.5)),
									e || h || this.adjustTickAmount()),
								C(this, "afterSetTickPositions");
						}
						trimTicks(t, e, i) {
							let s = t[0],
								r = t[t.length - 1],
								o = (!this.isOrdinal && this.minPointOffset) || 0;
							if ((C(this, "trimTicks"), !this.isLinked)) {
								if (e && s !== -1 / 0) this.min = s;
								else for (; this.min - o > t[0]; ) t.shift();
								if (i) this.max = r;
								else for (; this.max + o < t[t.length - 1]; ) t.pop();
								0 === t.length &&
									y(s) &&
									!this.options.tickPositions &&
									t.push((r + s) / 2);
							}
						}
						alignToOthers() {
							let t;
							let e = this,
								i = e.chart,
								s = [this],
								r = e.options,
								o = i.options.chart,
								a = "yAxis" === this.coll && o.alignThresholds,
								n = [];
							if (
								((e.thresholdAlignment = void 0),
								((!1 !== o.alignTicks && r.alignTicks) || a) &&
									!1 !== r.startOnTick &&
									!1 !== r.endOnTick &&
									!e.logarithmic)
							) {
								let r = (t) => {
										let { horiz: e, options: i } = t;
										return [e ? i.left : i.top, i.width, i.height, i.pane].join(
											","
										);
									},
									o = r(this);
								i[this.coll].forEach(function (i) {
									let { series: a } = i;
									a.length &&
										a.some((t) => t.visible) &&
										i !== e &&
										r(i) === o &&
										((t = !0), s.push(i));
								});
							}
							if (t && a) {
								s.forEach((t) => {
									let i = t.getThresholdAlignment(e);
									A(i) && n.push(i);
								});
								let t =
									n.length > 1
										? n.reduce((t, e) => (t += e), 0) / n.length
										: void 0;
								s.forEach((e) => {
									e.thresholdAlignment = t;
								});
							}
							return t;
						}
						getThresholdAlignment(t) {
							if (
								((!A(this.dataMin) ||
									(this !== t &&
										this.series.some((t) => t.isDirty || t.isDirtyData))) &&
									this.getSeriesExtremes(),
								A(this.threshold))
							) {
								let t = m(
									(this.threshold - (this.dataMin || 0)) /
										((this.dataMax || 0) - (this.dataMin || 0)),
									0,
									1
								);
								return this.options.reversed && (t = 1 - t), t;
							}
						}
						getTickAmount() {
							let t = this.options,
								e = t.tickPixelInterval,
								i = t.tickAmount;
							y(t.tickInterval) ||
								i ||
								!(this.len < e) ||
								this.isRadial ||
								this.logarithmic ||
								!t.startOnTick ||
								!t.endOnTick ||
								(i = 2),
								!i && this.alignToOthers() && (i = Math.ceil(this.len / e) + 1),
								i < 4 && ((this.finalTickAmt = i), (i = 5)),
								(this.tickAmount = i);
						}
						adjustTickAmount() {
							let t = this,
								{
									finalTickAmt: e,
									max: i,
									min: s,
									options: r,
									tickPositions: o,
									tickAmount: a,
									thresholdAlignment: n,
								} = t,
								h = o?.length,
								l = E(t.threshold, t.softThreshold ? 0 : null),
								d,
								c,
								p = t.tickInterval,
								u,
								g = () => o.push(x(o[o.length - 1] + p)),
								f = () => o.unshift(x(o[0] - p));
							if (
								(A(n) &&
									((u =
										n < 0.5 ? Math.ceil(n * (a - 1)) : Math.floor(n * (a - 1))),
									r.reversed && (u = a - 1 - u)),
								t.hasData() && A(s) && A(i))
							) {
								let n = () => {
									(t.transA *= (h - 1) / (a - 1)),
										(t.min = r.startOnTick ? o[0] : Math.min(s, o[0])),
										(t.max = r.endOnTick
											? o[o.length - 1]
											: Math.max(i, o[o.length - 1]));
								};
								if (A(u) && A(t.threshold)) {
									for (
										;
										o[u] !== l ||
										o.length !== a ||
										o[0] > s ||
										o[o.length - 1] < i;

									) {
										for (o.length = 0, o.push(t.threshold); o.length < a; )
											void 0 === o[u] || o[u] > t.threshold ? f() : g();
										if (p > 8 * t.tickInterval) break;
										p *= 2;
									}
									n();
								} else if (h < a) {
									for (; o.length < a; ) o.length % 2 || s === l ? g() : f();
									n();
								}
								if (y(e)) {
									for (c = d = o.length; c--; )
										((3 === e && c % 2 == 1) ||
											(e <= 2 && c > 0 && c < d - 1)) &&
											o.splice(c, 1);
									t.finalTickAmt = void 0;
								}
							}
						}
						setScale() {
							let { coll: t, stacking: e } = this,
								i = !1,
								s = !1;
							this.series.forEach((t) => {
								(i = i || t.isDirtyData || t.isDirty),
									(s = s || (t.xAxis && t.xAxis.isDirty) || !1);
							}),
								this.setAxisSize();
							let r = this.len !== (this.old && this.old.len);
							r ||
							i ||
							s ||
							this.isLinked ||
							this.forceRedraw ||
							this.userMin !== (this.old && this.old.userMin) ||
							this.userMax !== (this.old && this.old.userMax) ||
							this.alignToOthers()
								? (e && "yAxis" === t && e.buildStacks(),
								  (this.forceRedraw = !1),
								  this.userMinRange || (this.minRange = void 0),
								  this.getSeriesExtremes(),
								  this.setTickInterval(),
								  e && "xAxis" === t && e.buildStacks(),
								  this.isDirty ||
										(this.isDirty =
											r ||
											this.min !== this.old?.min ||
											this.max !== this.old?.max))
								: e && e.cleanStacks(),
								i && delete this.allExtremes,
								C(this, "afterSetScale");
						}
						setExtremes(t, e, i = !0, s, r) {
							this.series.forEach((t) => {
								delete t.kdTree;
							}),
								C(this, "setExtremes", (r = k(r, { min: t, max: e })), (t) => {
									(this.userMin = t.min),
										(this.userMax = t.max),
										(this.eventArgs = t),
										i && this.chart.redraw(s);
								});
						}
						setAxisSize() {
							let t = this.chart,
								e = this.options,
								i = e.offsets || [0, 0, 0, 0],
								s = this.horiz,
								r = (this.width = Math.round(
									I(E(e.width, t.plotWidth - i[3] + i[1]), t.plotWidth)
								)),
								o = (this.height = Math.round(
									I(E(e.height, t.plotHeight - i[0] + i[2]), t.plotHeight)
								)),
								a = (this.top = Math.round(
									I(E(e.top, t.plotTop + i[0]), t.plotHeight, t.plotTop)
								)),
								n = (this.left = Math.round(
									I(E(e.left, t.plotLeft + i[3]), t.plotWidth, t.plotLeft)
								));
							(this.bottom = t.chartHeight - o - a),
								(this.right = t.chartWidth - r - n),
								(this.len = Math.max(s ? r : o, 0)),
								(this.pos = s ? n : a);
						}
						getExtremes() {
							let t = this.logarithmic;
							return {
								min: t ? x(t.lin2log(this.min)) : this.min,
								max: t ? x(t.lin2log(this.max)) : this.max,
								dataMin: this.dataMin,
								dataMax: this.dataMax,
								userMin: this.userMin,
								userMax: this.userMax,
							};
						}
						getThreshold(t) {
							let e = this.logarithmic,
								i = e ? e.lin2log(this.min) : this.min,
								s = e ? e.lin2log(this.max) : this.max;
							return (
								null === t || t === -1 / 0
									? (t = i)
									: t === 1 / 0
									? (t = s)
									: i > t
									? (t = i)
									: s < t && (t = s),
								this.translate(t, 0, 1, 0, 1)
							);
						}
						autoLabelAlign(t) {
							let e = (E(t, 0) - 90 * this.side + 720) % 360,
								i = { align: "center" };
							return (
								C(this, "autoLabelAlign", i, function (t) {
									e > 15 && e < 165
										? (t.align = "right")
										: e > 195 && e < 345 && (t.align = "left");
								}),
								i.align
							);
						}
						tickSize(t) {
							let e = this.options,
								i = E(
									e["tick" === t ? "tickWidth" : "minorTickWidth"],
									"tick" === t && this.isXAxis && !this.categories ? 1 : 0
								),
								s = e["tick" === t ? "tickLength" : "minorTickLength"],
								r;
							i &&
								s &&
								("inside" === e[t + "Position"] && (s = -s), (r = [s, i]));
							let o = { tickSize: r };
							return C(this, "afterTickSize", o), o.tickSize;
						}
						labelMetrics() {
							let t = this.chart.renderer,
								e = this.ticks,
								i = e[Object.keys(e)[0]] || {};
							return this.chart.renderer.fontMetrics(
								i.label || i.movedLabel || t.box
							);
						}
						unsquish() {
							let t = this.options.labels,
								e = this.horiz,
								i = this.tickInterval,
								s =
									this.len /
									(((this.categories ? 1 : 0) + this.max - this.min) / i),
								r = t.rotation,
								o = this.labelMetrics().h,
								a = Math.max(this.max - this.min, 0),
								n = function (t) {
									let e = t / (s || 1);
									return (
										(e = e > 1 ? Math.ceil(e) : 1) * i > a &&
											t !== 1 / 0 &&
											s !== 1 / 0 &&
											a &&
											(e = Math.ceil(a / i)),
										x(e * i)
									);
								},
								h = i,
								l,
								d = Number.MAX_VALUE,
								c;
							if (e) {
								if (
									(!t.staggerLines &&
										(A(r)
											? (c = [r])
											: s < t.autoRotationLimit && (c = t.autoRotation)),
									c)
								) {
									let t, e;
									for (let i of c)
										(i === r || (i && i >= -90 && i <= 90)) &&
											(e =
												(t = n(Math.abs(o / Math.sin(u * i)))) +
												Math.abs(i / 360)) < d &&
											((d = e), (l = i), (h = t));
								}
							} else h = n(0.75 * o);
							return (
								(this.autoRotation = c),
								(this.labelRotation = E(l, A(r) ? r : 0)),
								t.step ? i : h
							);
						}
						getSlotWidth(t) {
							let e = this.chart,
								i = this.horiz,
								s = this.options.labels,
								r = Math.max(
									this.tickPositions.length - (this.categories ? 0 : 1),
									1
								),
								o = e.margin[3];
							if (t && A(t.slotWidth)) return t.slotWidth;
							if (i && s.step < 2)
								return s.rotation
									? 0
									: ((this.staggerLines || 1) * this.len) / r;
							if (!i) {
								let t = s.style.width;
								if (void 0 !== t) return parseInt(String(t), 10);
								if (o) return o - e.spacing[3];
							}
							return 0.33 * e.chartWidth;
						}
						renderUnsquish() {
							let t = this.chart,
								e = t.renderer,
								i = this.tickPositions,
								s = this.ticks,
								r = this.options.labels,
								o = r.style,
								a = this.horiz,
								n = this.getSlotWidth(),
								h = Math.max(1, Math.round(n - 2 * r.padding)),
								l = {},
								d = this.labelMetrics(),
								c = o.textOverflow,
								p,
								u,
								g = 0,
								f,
								m;
							if (
								(P(r.rotation) || (l.rotation = r.rotation || 0),
								i.forEach(function (t) {
									let e = s[t];
									e.movedLabel && e.replaceMovedLabel(),
										e &&
											e.label &&
											e.label.textPxLength > g &&
											(g = e.label.textPxLength);
								}),
								(this.maxLabelLength = g),
								this.autoRotation)
							)
								g > h && g > d.h
									? (l.rotation = this.labelRotation)
									: (this.labelRotation = 0);
							else if (n && ((p = h), !c))
								for (u = "clip", m = i.length; !a && m--; )
									(f = s[i[m]].label) &&
										("ellipsis" === f.styles.textOverflow
											? f.css({ textOverflow: "clip" })
											: f.textPxLength > n && f.css({ width: n + "px" }),
										f.getBBox().height > this.len / i.length - (d.h - d.f) &&
											(f.specificTextOverflow = "ellipsis"));
							l.rotation &&
								((p = g > 0.5 * t.chartHeight ? 0.33 * t.chartHeight : g),
								c || (u = "ellipsis")),
								(this.labelAlign =
									r.align || this.autoLabelAlign(this.labelRotation)),
								this.labelAlign && (l.align = this.labelAlign),
								i.forEach(function (t) {
									let e = s[t],
										i = e && e.label,
										r = o.width,
										a = {};
									i &&
										(i.attr(l),
										e.shortenLabel
											? e.shortenLabel()
											: p &&
											  !r &&
											  "nowrap" !== o.whiteSpace &&
											  (p < i.textPxLength || "SPAN" === i.element.tagName)
											? ((a.width = p + "px"),
											  c || (a.textOverflow = i.specificTextOverflow || u),
											  i.css(a))
											: !i.styles.width ||
											  a.width ||
											  r ||
											  i.css({ width: null }),
										delete i.specificTextOverflow,
										(e.rotation = l.rotation));
								}, this),
								(this.tickRotCorr = e.rotCorr(
									d.b,
									this.labelRotation || 0,
									0 !== this.side
								));
						}
						hasData() {
							return (
								this.series.some(function (t) {
									return t.hasData();
								}) ||
								(this.options.showEmpty && y(this.min) && y(this.max))
							);
						}
						addTitle(t) {
							let e;
							let i = this.chart.renderer,
								s = this.horiz,
								r = this.opposite,
								o = this.options.title,
								a = this.chart.styledMode;
							this.axisTitle ||
								((e = o.textAlign) ||
									(e = (
										s
											? { low: "left", middle: "center", high: "right" }
											: {
													low: r ? "right" : "left",
													middle: "center",
													high: r ? "left" : "right",
											  }
									)[o.align]),
								(this.axisTitle = i
									.text(o.text || "", 0, 0, o.useHTML)
									.attr({ zIndex: 7, rotation: o.rotation || 0, align: e })
									.addClass("highcharts-axis-title")),
								a || this.axisTitle.css(L(o.style)),
								this.axisTitle.add(this.axisGroup),
								(this.axisTitle.isNew = !0)),
								a ||
									o.style.width ||
									this.isRadial ||
									this.axisTitle.css({ width: this.len + "px" }),
								this.axisTitle[t ? "show" : "hide"](t);
						}
						generateTick(t) {
							let e = this.ticks;
							e[t] ? e[t].addLabel() : (e[t] = new a(this, t));
						}
						createGroups() {
							let { axisParent: t, chart: e, coll: i, options: s } = this,
								r = e.renderer,
								o = (e, o, a) =>
									r
										.g(e)
										.attr({ zIndex: a })
										.addClass(
											`highcharts-${i.toLowerCase()}${o} ` +
												(this.isRadial ? `highcharts-radial-axis${o} ` : "") +
												(s.className || "")
										)
										.add(t);
							this.axisGroup ||
								((this.gridGroup = o("grid", "-grid", s.gridZIndex)),
								(this.axisGroup = o("axis", "", s.zIndex)),
								(this.labelGroup = o(
									"axis-labels",
									"-labels",
									s.labels.zIndex
								)));
						}
						getOffset() {
							let t = this,
								{
									chart: e,
									horiz: i,
									options: s,
									side: r,
									ticks: o,
									tickPositions: a,
									coll: n,
								} = t,
								h = e.inverted && !t.isZAxis ? [1, 0, 3, 2][r] : r,
								l = t.hasData(),
								d = s.title,
								c = s.labels,
								p = A(s.crossing),
								u = e.axisOffset,
								g = e.clipOffset,
								f = [-1, 1, 1, -1][r],
								m,
								x = 0,
								b,
								v = 0,
								S = 0,
								k,
								M;
							if (
								((t.showAxis = m = l || s.showEmpty),
								(t.staggerLines = (t.horiz && c.staggerLines) || void 0),
								t.createGroups(),
								l || t.isLinked
									? (a.forEach(function (e) {
											t.generateTick(e);
									  }),
									  t.renderUnsquish(),
									  (t.reserveSpaceDefault =
											0 === r ||
											2 === r ||
											{ 1: "left", 3: "right" }[r] === t.labelAlign),
									  E(
											c.reserveSpace,
											!p && null,
											"center" === t.labelAlign || null,
											t.reserveSpaceDefault
									  ) &&
											a.forEach(function (t) {
												S = Math.max(o[t].getLabelSize(), S);
											}),
									  t.staggerLines && (S *= t.staggerLines),
									  (t.labelOffset = S * (t.opposite ? -1 : 1)))
									: D(o, function (t, e) {
											t.destroy(), delete o[e];
									  }),
								d?.text &&
									!1 !== d.enabled &&
									(t.addTitle(m),
									m &&
										!p &&
										!1 !== d.reserveSpace &&
										((t.titleOffset = x =
											t.axisTitle.getBBox()[i ? "height" : "width"]),
										(v = y((b = d.offset)) ? 0 : E(d.margin, i ? 5 : 10)))),
								t.renderLine(),
								(t.offset = f * E(s.offset, u[r] ? u[r] + (s.margin || 0) : 0)),
								(t.tickRotCorr = t.tickRotCorr || { x: 0, y: 0 }),
								(M =
									0 === r
										? -t.labelMetrics().h
										: 2 === r
										? t.tickRotCorr.y
										: 0),
								(k = Math.abs(S) + v),
								S &&
									((k -= M),
									(k +=
										f *
										(i
											? E(c.y, t.tickRotCorr.y + f * c.distance)
											: E(c.x, f * c.distance)))),
								(t.axisTitleMargin = E(b, k)),
								t.getMaxLabelDimensions &&
									(t.maxLabelDimensions = t.getMaxLabelDimensions(o, a)),
								"colorAxis" !== n)
							) {
								let e = this.tickSize("tick");
								u[r] = Math.max(
									u[r],
									(t.axisTitleMargin || 0) + x + f * t.offset,
									k,
									a && a.length && e ? e[0] + f * t.offset : 0
								);
								let i =
									!t.axisLine || s.offset
										? 0
										: 2 * Math.floor(t.axisLine.strokeWidth() / 2);
								g[h] = Math.max(g[h], i);
							}
							C(this, "afterGetOffset");
						}
						getLinePath(t) {
							let e = this.chart,
								i = this.opposite,
								s = this.offset,
								r = this.horiz,
								o = this.left + (i ? this.width : 0) + s,
								a = e.chartHeight - this.bottom - (i ? this.height : 0) + s;
							return (
								i && (t *= -1),
								e.renderer.crispLine(
									[
										["M", r ? this.left : o, r ? a : this.top],
										[
											"L",
											r ? e.chartWidth - this.right : o,
											r ? a : e.chartHeight - this.bottom,
										],
									],
									t
								)
							);
						}
						renderLine() {
							this.axisLine ||
								((this.axisLine = this.chart.renderer
									.path()
									.addClass("highcharts-axis-line")
									.add(this.axisGroup)),
								this.chart.styledMode ||
									this.axisLine.attr({
										stroke: this.options.lineColor,
										"stroke-width": this.options.lineWidth,
										zIndex: 7,
									}));
						}
						getTitlePosition(t) {
							let e = this.horiz,
								i = this.left,
								s = this.top,
								r = this.len,
								o = this.options.title,
								a = e ? i : s,
								n = this.opposite,
								h = this.offset,
								l = o.x,
								d = o.y,
								c = this.chart.renderer.fontMetrics(t),
								p = t ? Math.max(t.getBBox(!1, 0).height - c.h - 1, 0) : 0,
								u = {
									low: a + (e ? 0 : r),
									middle: a + r / 2,
									high: a + (e ? r : 0),
								}[o.align],
								g =
									(e ? s + this.height : i) +
									(e ? 1 : -1) * (n ? -1 : 1) * (this.axisTitleMargin || 0) +
									[-p, p, c.f, -p][this.side],
								f = {
									x: e ? u + l : g + (n ? this.width : 0) + h + l,
									y: e ? g + d - (n ? this.height : 0) + h : u + d,
								};
							return C(this, "afterGetTitlePosition", { titlePosition: f }), f;
						}
						renderMinorTick(t, e) {
							let i = this.minorTicks;
							i[t] || (i[t] = new a(this, t, "minor")),
								e && i[t].isNew && i[t].render(null, !0),
								i[t].render(null, !1, 1);
						}
						renderTick(t, e, i) {
							let s = this.isLinked,
								r = this.ticks;
							(!s ||
								(t >= this.min && t <= this.max) ||
								(this.grid && this.grid.isColumn)) &&
								(r[t] || (r[t] = new a(this, t)),
								i && r[t].isNew && r[t].render(e, !0, -1),
								r[t].render(e));
						}
						render() {
							let t, e;
							let i = this,
								s = i.chart,
								r = i.logarithmic,
								n = s.renderer,
								l = i.options,
								d = i.isLinked,
								c = i.tickPositions,
								p = i.axisTitle,
								u = i.ticks,
								g = i.minorTicks,
								f = i.alternateBands,
								m = l.stackLabels,
								x = l.alternateGridColor,
								y = l.crossing,
								b = i.tickmarkOffset,
								v = i.axisLine,
								S = i.showAxis,
								k = h(n.globalAnimation);
							if (
								((i.labelEdge.length = 0),
								(i.overlap = !1),
								[u, g, f].forEach(function (t) {
									D(t, function (t) {
										t.isActive = !1;
									});
								}),
								A(y))
							) {
								let t = this.isXAxis ? s.yAxis[0] : s.xAxis[0],
									e = [1, -1, -1, 1][this.side];
								if (t) {
									let s = t.toPixels(y, !0);
									i.horiz && (s = t.len - s), (i.offset = e * s);
								}
							}
							if (i.hasData() || d) {
								let n = i.chart.hasRendered && i.old && A(i.old.min);
								i.minorTickInterval &&
									!i.categories &&
									i.getMinorTickPositions().forEach(function (t) {
										i.renderMinorTick(t, n);
									}),
									c.length &&
										(c.forEach(function (t, e) {
											i.renderTick(t, e, n);
										}),
										b &&
											(0 === i.min || i.single) &&
											(u[-1] || (u[-1] = new a(i, -1, null, !0)),
											u[-1].render(-1))),
									x &&
										c.forEach(function (a, n) {
											(e = void 0 !== c[n + 1] ? c[n + 1] + b : i.max - b),
												n % 2 == 0 &&
													a < i.max &&
													e <= i.max + (s.polar ? -b : b) &&
													(f[a] || (f[a] = new o.PlotLineOrBand(i, {})),
													(t = a + b),
													(f[a].options = {
														from: r ? r.lin2log(t) : t,
														to: r ? r.lin2log(e) : e,
														color: x,
														className: "highcharts-alternate-grid",
													}),
													f[a].render(),
													(f[a].isActive = !0));
										}),
									i._addedPlotLB ||
										((i._addedPlotLB = !0),
										(l.plotLines || [])
											.concat(l.plotBands || [])
											.forEach(function (t) {
												i.addPlotBandOrLine(t);
											}));
							}
							[u, g, f].forEach(function (t) {
								let e = [],
									i = k.duration;
								D(t, function (t, i) {
									t.isActive ||
										(t.render(i, !1, 0), (t.isActive = !1), e.push(i));
								}),
									R(
										function () {
											let i = e.length;
											for (; i--; )
												t[e[i]] &&
													!t[e[i]].isActive &&
													(t[e[i]].destroy(), delete t[e[i]]);
										},
										t !== f && s.hasRendered && i ? i : 0
									);
							}),
								v &&
									(v[v.isPlaced ? "animate" : "attr"]({
										d: this.getLinePath(v.strokeWidth()),
									}),
									(v.isPlaced = !0),
									v[S ? "show" : "hide"](S)),
								p &&
									S &&
									(p[p.isNew ? "attr" : "animate"](i.getTitlePosition(p)),
									(p.isNew = !1)),
								m && m.enabled && i.stacking && i.stacking.renderStackTotals(),
								(i.old = {
									len: i.len,
									max: i.max,
									min: i.min,
									transA: i.transA,
									userMax: i.userMax,
									userMin: i.userMin,
								}),
								(i.isDirty = !1),
								C(this, "afterRender");
						}
						redraw() {
							this.visible &&
								(this.render(),
								this.plotLinesAndBands.forEach(function (t) {
									t.render();
								})),
								this.series.forEach(function (t) {
									t.isDirty = !0;
								});
						}
						getKeepProps() {
							return this.keepProps || N.keepProps;
						}
						destroy(t) {
							let e = this,
								i = e.plotLinesAndBands,
								s = this.eventOptions;
							if (
								(C(this, "destroy", { keepEvents: t }),
								t || j(e),
								[e.ticks, e.minorTicks, e.alternateBands].forEach(function (t) {
									b(t);
								}),
								i)
							) {
								let t = i.length;
								for (; t--; ) i[t].destroy();
							}
							for (let t in ([
								"axisLine",
								"axisTitle",
								"axisGroup",
								"gridGroup",
								"labelGroup",
								"cross",
								"scrollbar",
							].forEach(function (t) {
								e[t] && (e[t] = e[t].destroy());
							}),
							e.plotLinesAndBandsGroups))
								e.plotLinesAndBandsGroups[t] =
									e.plotLinesAndBandsGroups[t].destroy();
							D(e, function (t, i) {
								-1 === e.getKeepProps().indexOf(i) && delete e[i];
							}),
								(this.eventOptions = s);
						}
						drawCrosshair(t, e) {
							let s = this.crosshair,
								r = E(s && s.snap, !0),
								o = this.chart,
								a,
								n,
								h,
								l = this.cross,
								d;
							if (
								(C(this, "drawCrosshair", { e: t, point: e }),
								t || (t = this.cross && this.cross.e),
								s && !1 !== (y(e) || !r))
							) {
								if (
									(r
										? y(e) &&
										  (n = E(
												"colorAxis" !== this.coll ? e.crosshairPos : null,
												this.isXAxis ? e.plotX : this.len - e.plotY
										  ))
										: (n =
												t &&
												(this.horiz
													? t.chartX - this.pos
													: this.len - t.chartY + this.pos)),
									y(n) &&
										((d = {
											value: e && (this.isXAxis ? e.x : E(e.stackY, e.y)),
											translatedValue: n,
										}),
										o.polar &&
											k(d, {
												isCrosshair: !0,
												chartX: t && t.chartX,
												chartY: t && t.chartY,
												point: e,
											}),
										(a = this.getPlotLinePath(d) || null)),
									!y(a))
								) {
									this.hideCrosshair();
									return;
								}
								(h = this.categories && !this.isRadial),
									l ||
										((this.cross = l =
											o.renderer
												.path()
												.addClass(
													"highcharts-crosshair highcharts-crosshair-" +
														(h ? "category " : "thin ") +
														(s.className || "")
												)
												.attr({ zIndex: E(s.zIndex, 2) })
												.add()),
										!o.styledMode &&
											(l
												.attr({
													stroke:
														s.color ||
														(h
															? i.parse("#ccd3ff").setOpacity(0.25).get()
															: "#cccccc"),
													"stroke-width": E(s.width, 1),
												})
												.css({ "pointer-events": "none" }),
											s.dashStyle && l.attr({ dashstyle: s.dashStyle }))),
									l.show().attr({ d: a }),
									h && !s.width && l.attr({ "stroke-width": this.transA }),
									(this.cross.e = t);
							} else this.hideCrosshair();
							C(this, "afterDrawCrosshair", { e: t, point: e });
						}
						hideCrosshair() {
							this.cross && this.cross.hide(), C(this, "afterHideCrosshair");
						}
						update(t, e) {
							let i = this.chart;
							(t = L(this.userOptions, t)),
								this.destroy(!0),
								this.init(i, t),
								(i.isDirtyBox = !0),
								E(e, !0) && i.redraw();
						}
						remove(t) {
							let e = this.chart,
								i = this.coll,
								s = this.series,
								r = s.length;
							for (; r--; ) s[r] && s[r].remove(!1);
							v(e.axes, this),
								v(e[i] || [], this),
								e.orderItems(i),
								this.destroy(),
								(e.isDirtyBox = !0),
								E(t, !0) && e.redraw();
						}
						setTitle(t, e) {
							this.update({ title: t }, e);
						}
						setCategories(t, e) {
							this.update({ categories: t }, e);
						}
					}
					return (
						(N.keepProps = [
							"coll",
							"extKey",
							"hcEvents",
							"names",
							"series",
							"userMax",
							"userMin",
						]),
						N
					);
				}
			),
			i(e, "Core/Axis/DateTimeAxis.js", [e["Core/Utilities.js"]], function (t) {
				var e;
				let {
					addEvent: i,
					getMagnitude: s,
					normalizeTickInterval: r,
					timeUnits: o,
				} = t;
				return (
					(function (t) {
						function e() {
							return this.chart.time.getTimeTicks.apply(
								this.chart.time,
								arguments
							);
						}
						function a() {
							if ("datetime" !== this.options.type) {
								this.dateTime = void 0;
								return;
							}
							this.dateTime || (this.dateTime = new n(this));
						}
						t.compose = function (t) {
							return (
								t.keepProps.includes("dateTime") ||
									(t.keepProps.push("dateTime"),
									(t.prototype.getTimeTicks = e),
									i(t, "afterSetOptions", a)),
								t
							);
						};
						class n {
							constructor(t) {
								this.axis = t;
							}
							normalizeTimeTickInterval(t, e) {
								let i = e || [
										["millisecond", [1, 2, 5, 10, 20, 25, 50, 100, 200, 500]],
										["second", [1, 2, 5, 10, 15, 30]],
										["minute", [1, 2, 5, 10, 15, 30]],
										["hour", [1, 2, 3, 4, 6, 8, 12]],
										["day", [1, 2]],
										["week", [1, 2]],
										["month", [1, 2, 3, 4, 6]],
										["year", null],
									],
									a = i[i.length - 1],
									n = o[a[0]],
									h = a[1],
									l;
								for (
									l = 0;
									l < i.length &&
									((n = o[(a = i[l])[0]]),
									(h = a[1]),
									!i[l + 1] ||
										!(t <= (n * h[h.length - 1] + o[i[l + 1][0]]) / 2));
									l++
								);
								n === o.year && t < 5 * n && (h = [1, 2, 5]);
								let d = r(
									t / n,
									h,
									"year" === a[0] ? Math.max(s(t / n), 1) : 1
								);
								return { unitRange: n, count: d, unitName: a[0] };
							}
							getXDateFormat(t, e) {
								let { axis: i } = this,
									s = i.chart.time;
								return i.closestPointRange
									? s.getDateFormat(
											i.closestPointRange,
											t,
											i.options.startOfWeek,
											e
									  ) || s.resolveDTLFormat(e.year).main
									: s.resolveDTLFormat(e.day).main;
							}
						}
						t.Additions = n;
					})(e || (e = {})),
					e
				);
			}),
			i(
				e,
				"Core/Axis/LogarithmicAxis.js",
				[e["Core/Utilities.js"]],
				function (t) {
					var e;
					let { addEvent: i, normalizeTickInterval: s, pick: r } = t;
					return (
						(function (t) {
							function e(t) {
								let e = t.userOptions,
									i = this.logarithmic;
								"logarithmic" !== e.type
									? (this.logarithmic = void 0)
									: i || (i = this.logarithmic = new a(this));
							}
							function o() {
								let t = this.logarithmic;
								t &&
									((this.lin2val = function (e) {
										return t.lin2log(e);
									}),
									(this.val2lin = function (e) {
										return t.log2lin(e);
									}));
							}
							t.compose = function (t) {
								return (
									t.keepProps.includes("logarithmic") ||
										(t.keepProps.push("logarithmic"),
										i(t, "init", e),
										i(t, "afterInit", o)),
									t
								);
							};
							class a {
								constructor(t) {
									this.axis = t;
								}
								getLogTickPositions(t, e, i, o) {
									let a = this.axis,
										n = a.len,
										h = a.options,
										l = [];
									if ((o || (this.minorAutoInterval = void 0), t >= 0.5))
										(t = Math.round(t)),
											(l = a.getLinearTickPositions(t, e, i));
									else if (t >= 0.08) {
										let s, r, a, n, h, d, c;
										let p = Math.floor(e);
										for (
											s =
												t > 0.3
													? [1, 2, 4]
													: t > 0.15
													? [1, 2, 4, 6, 8]
													: [1, 2, 3, 4, 5, 6, 7, 8, 9],
												r = p;
											r < i + 1 && !c;
											r++
										)
											for (a = 0, n = s.length; a < n && !c; a++)
												(h = this.log2lin(this.lin2log(r) * s[a])) > e &&
													(!o || d <= i) &&
													void 0 !== d &&
													l.push(d),
													d > i && (c = !0),
													(d = h);
									} else {
										let d = this.lin2log(e),
											c = this.lin2log(i),
											p = o ? a.getMinorTickInterval() : h.tickInterval,
											u = h.tickPixelInterval / (o ? 5 : 1),
											g = o ? n / a.tickPositions.length : n;
										(t = s(
											(t = r(
												"auto" === p ? null : p,
												this.minorAutoInterval,
												((c - d) * u) / (g || 1)
											))
										)),
											(l = a.getLinearTickPositions(t, d, c).map(this.log2lin)),
											o || (this.minorAutoInterval = t / 5);
									}
									return o || (a.tickInterval = t), l;
								}
								lin2log(t) {
									return Math.pow(10, t);
								}
								log2lin(t) {
									return Math.log(t) / Math.LN10;
								}
							}
							t.Additions = a;
						})(e || (e = {})),
						e
					);
				}
			),
			i(
				e,
				"Core/Axis/PlotLineOrBand/PlotLineOrBandAxis.js",
				[e["Core/Utilities.js"]],
				function (t) {
					var e;
					let { erase: i, extend: s, isNumber: r } = t;
					return (
						(function (t) {
							let e;
							function o(t) {
								return this.addPlotBandOrLine(t, "plotBands");
							}
							function a(t, i) {
								let s = this.userOptions,
									r = new e(this, t);
								if ((this.visible && (r = r.render()), r)) {
									if (
										(this._addedPlotLB ||
											((this._addedPlotLB = !0),
											(s.plotLines || [])
												.concat(s.plotBands || [])
												.forEach((t) => {
													this.addPlotBandOrLine(t);
												})),
										i)
									) {
										let e = s[i] || [];
										e.push(t), (s[i] = e);
									}
									this.plotLinesAndBands.push(r);
								}
								return r;
							}
							function n(t) {
								return this.addPlotBandOrLine(t, "plotLines");
							}
							function h(t, e, i) {
								i = i || this.options;
								let s = this.getPlotLinePath({
										value: e,
										force: !0,
										acrossPanes: i.acrossPanes,
									}),
									o = [],
									a = this.horiz,
									n =
										!r(this.min) ||
										!r(this.max) ||
										(t < this.min && e < this.min) ||
										(t > this.max && e > this.max),
									h = this.getPlotLinePath({
										value: t,
										force: !0,
										acrossPanes: i.acrossPanes,
									}),
									l,
									d = 1,
									c;
								if (h && s)
									for (
										n && ((c = h.toString() === s.toString()), (d = 0)), l = 0;
										l < h.length;
										l += 2
									) {
										let t = h[l],
											e = h[l + 1],
											i = s[l],
											r = s[l + 1];
										("M" === t[0] || "L" === t[0]) &&
											("M" === e[0] || "L" === e[0]) &&
											("M" === i[0] || "L" === i[0]) &&
											("M" === r[0] || "L" === r[0]) &&
											(a && i[1] === t[1]
												? ((i[1] += d), (r[1] += d))
												: a || i[2] !== t[2] || ((i[2] += d), (r[2] += d)),
											o.push(
												["M", t[1], t[2]],
												["L", e[1], e[2]],
												["L", r[1], r[2]],
												["L", i[1], i[2]],
												["Z"]
											)),
											(o.isFlat = c);
									}
								return o;
							}
							function l(t) {
								this.removePlotBandOrLine(t);
							}
							function d(t) {
								let e = this.plotLinesAndBands,
									s = this.options,
									r = this.userOptions;
								if (e) {
									let o = e.length;
									for (; o--; ) e[o].id === t && e[o].destroy();
									[
										s.plotLines || [],
										r.plotLines || [],
										s.plotBands || [],
										r.plotBands || [],
									].forEach(function (e) {
										for (o = e.length; o--; )
											(e[o] || {}).id === t && i(e, e[o]);
									});
								}
							}
							function c(t) {
								this.removePlotBandOrLine(t);
							}
							t.compose = function (t, i) {
								let r = i.prototype;
								return (
									r.addPlotBand ||
										((e = t),
										s(r, {
											addPlotBand: o,
											addPlotLine: n,
											addPlotBandOrLine: a,
											getPlotBandPath: h,
											removePlotBand: l,
											removePlotLine: c,
											removePlotBandOrLine: d,
										})),
									i
								);
							};
						})(e || (e = {})),
						e
					);
				}
			),
			i(
				e,
				"Core/Axis/PlotLineOrBand/PlotLineOrBand.js",
				[
					e["Core/Axis/PlotLineOrBand/PlotLineOrBandAxis.js"],
					e["Core/Utilities.js"],
				],
				function (t, e) {
					let {
						arrayMax: i,
						arrayMin: s,
						defined: r,
						destroyObjectProperties: o,
						erase: a,
						fireEvent: n,
						merge: h,
						objectEach: l,
						pick: d,
					} = e;
					class c {
						static compose(e) {
							return t.compose(c, e);
						}
						constructor(t, e) {
							(this.axis = t), (this.options = e), (this.id = e.id);
						}
						render() {
							n(this, "render");
							let { axis: t, options: e } = this,
								{ horiz: i, logarithmic: s } = t,
								{ color: o, events: a, zIndex: c = 0 } = e,
								p = {},
								u = t.chart.renderer,
								g = e.to,
								f = e.from,
								m = e.value,
								x = e.borderWidth,
								y = e.label,
								{ label: b, svgElem: v } = this,
								S = [],
								k,
								C = r(f) && r(g),
								M = r(m),
								w = !v,
								T = {
									class:
										"highcharts-plot-" +
										(C ? "band " : "line ") +
										(e.className || ""),
								},
								A = C ? "bands" : "lines";
							if (
								(!t.chart.styledMode &&
									(M
										? ((T.stroke = o || "#999999"),
										  (T["stroke-width"] = d(e.width, 1)),
										  e.dashStyle && (T.dashstyle = e.dashStyle))
										: C &&
										  ((T.fill = o || "#e6e9ff"),
										  x &&
												((T.stroke = e.borderColor), (T["stroke-width"] = x)))),
								(p.zIndex = c),
								(A += "-" + c),
								(k = t.plotLinesAndBandsGroups[A]) ||
									(t.plotLinesAndBandsGroups[A] = k =
										u
											.g("plot-" + A)
											.attr(p)
											.add()),
								v || (this.svgElem = v = u.path().attr(T).add(k)),
								r(m))
							)
								S = t.getPlotLinePath({
									value: s?.log2lin(m) ?? m,
									lineWidth: v.strokeWidth(),
									acrossPanes: e.acrossPanes,
								});
							else {
								if (!(r(f) && r(g))) return;
								S = t.getPlotBandPath(
									s?.log2lin(f) ?? f,
									s?.log2lin(g) ?? g,
									e
								);
							}
							return (
								!this.eventsAdded &&
									a &&
									(l(a, (t, e) => {
										v?.on(e, (t) => {
											a[e].apply(this, [t]);
										});
									}),
									(this.eventsAdded = !0)),
								(w || !v.d) && S?.length
									? v.attr({ d: S })
									: v &&
									  (S
											? (v.show(), v.animate({ d: S }))
											: v.d && (v.hide(), b && (this.label = b = b.destroy()))),
								y &&
								(r(y.text) || r(y.formatter)) &&
								S?.length &&
								t.width > 0 &&
								t.height > 0 &&
								!S.isFlat
									? ((y = h(
											{
												align: i && C && "center",
												x: i ? !C && 4 : 10,
												verticalAlign: !i && C && "middle",
												y: i ? (C ? 16 : 10) : C ? 6 : -4,
												rotation: i && !C && 90,
											},
											y
									  )),
									  this.renderLabel(y, S, C, c))
									: b && b.hide(),
								this
							);
						}
						renderLabel(t, e, r, o) {
							let a = this.axis,
								n = a.chart.renderer,
								l = this.label;
							l ||
								((this.label = l =
									n
										.text(this.getLabelText(t), 0, 0, t.useHTML)
										.attr({
											align: t.textAlign || t.align,
											rotation: t.rotation,
											class:
												"highcharts-plot-" +
												(r ? "band" : "line") +
												"-label" +
												(t.className || ""),
											zIndex: o,
										})),
								a.chart.styledMode ||
									l.css(
										h({ fontSize: "0.8em", textOverflow: "ellipsis" }, t.style)
									),
								l.add());
							let d = e.xBounds || [e[0][1], e[1][1], r ? e[2][1] : e[0][1]],
								c = e.yBounds || [e[0][2], e[1][2], r ? e[2][2] : e[0][2]],
								p = s(d),
								u = s(c);
							if (
								(l.align(t, !1, {
									x: p,
									y: u,
									width: i(d) - p,
									height: i(c) - u,
								}),
								!l.alignValue || "left" === l.alignValue)
							) {
								let e = t.clip ? a.width : a.chart.chartWidth;
								l.css({
									width:
										(90 === l.rotation
											? a.height - (l.alignAttr.y - a.top)
											: e - (l.alignAttr.x - a.left)) + "px",
								});
							}
							l.show(!0);
						}
						getLabelText(t) {
							return r(t.formatter) ? t.formatter.call(this) : t.text;
						}
						destroy() {
							a(this.axis.plotLinesAndBands, this), delete this.axis, o(this);
						}
					}
					return c;
				}
			),
			i(
				e,
				"Core/Tooltip.js",
				[
					e["Core/Templating.js"],
					e["Core/Globals.js"],
					e["Core/Renderer/RendererUtilities.js"],
					e["Core/Renderer/RendererRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r) {
					var o;
					let { format: a } = t,
						{ composed: n, doc: h, isSafari: l } = e,
						{ distribute: d } = i,
						{
							addEvent: c,
							clamp: p,
							css: u,
							discardElement: g,
							extend: f,
							fireEvent: m,
							isArray: x,
							isNumber: y,
							isString: b,
							merge: v,
							pick: S,
							pushUnique: k,
							splat: C,
							syncTimeout: M,
						} = r;
					class w {
						constructor(t, e, i) {
							(this.allowShared = !0),
								(this.crosshairs = []),
								(this.distance = 0),
								(this.isHidden = !0),
								(this.isSticky = !1),
								(this.now = {}),
								(this.options = {}),
								(this.outside = !1),
								(this.chart = t),
								this.init(t, e),
								(this.pointer = i);
						}
						bodyFormatter(t) {
							return t.map(function (t) {
								let e = t.series.tooltipOptions;
								return (
									e[(t.point.formatPrefix || "point") + "Formatter"] ||
									t.point.tooltipFormatter
								).call(
									t.point,
									e[(t.point.formatPrefix || "point") + "Format"] || ""
								);
							});
						}
						cleanSplit(t) {
							this.chart.series.forEach(function (e) {
								let i = e && e.tt;
								i &&
									(!i.isActive || t ? (e.tt = i.destroy()) : (i.isActive = !1));
							});
						}
						defaultFormatter(t) {
							let e;
							let i = this.points || C(this);
							return (
								(e = (e = [t.tooltipFooterHeaderFormatter(i[0])]).concat(
									t.bodyFormatter(i)
								)).push(t.tooltipFooterHeaderFormatter(i[0], !0)),
								e
							);
						}
						destroy() {
							this.label && (this.label = this.label.destroy()),
								this.split &&
									(this.cleanSplit(!0),
									this.tt && (this.tt = this.tt.destroy())),
								this.renderer &&
									((this.renderer = this.renderer.destroy()),
									g(this.container)),
								r.clearTimeout(this.hideTimer),
								r.clearTimeout(this.tooltipTimeout);
						}
						getAnchor(t, e) {
							let i;
							let { chart: s, pointer: r } = this,
								o = s.inverted,
								a = s.plotTop,
								n = s.plotLeft;
							if (
								((t = C(t))[0].series &&
									t[0].series.yAxis &&
									!t[0].series.yAxis.options.reversedStacks &&
									(t = t.slice().reverse()),
								this.followPointer && e)
							)
								void 0 === e.chartX && (e = r.normalize(e)),
									(i = [e.chartX - n, e.chartY - a]);
							else if (t[0].tooltipPos) i = t[0].tooltipPos;
							else {
								let s = 0,
									r = 0;
								t.forEach(function (t) {
									let e = t.pos(!0);
									e && ((s += e[0]), (r += e[1]));
								}),
									(s /= t.length),
									(r /= t.length),
									this.shared &&
										t.length > 1 &&
										e &&
										(o ? (s = e.chartX) : (r = e.chartY)),
									(i = [s - n, r - a]);
							}
							return i.map(Math.round);
						}
						getClassName(t, e, i) {
							let s = this.options,
								r = t.series,
								o = r.options;
							return [
								s.className,
								"highcharts-label",
								i && "highcharts-tooltip-header",
								e ? "highcharts-tooltip-box" : "highcharts-tooltip",
								!i && "highcharts-color-" + S(t.colorIndex, r.colorIndex),
								o && o.className,
							]
								.filter(b)
								.join(" ");
						}
						getLabel() {
							let t = this,
								i = this.chart.styledMode,
								r = this.options,
								o = this.split && this.allowShared,
								a = this.container,
								n = this.chart.renderer;
							if (this.label) {
								let t = !this.label.hasClass("highcharts-label");
								((!o && t) || (o && !t)) && this.destroy();
							}
							if (!this.label) {
								if (this.outside) {
									let t = this.chart.options.chart.style,
										i = s.getRendererType();
									(this.container = a = e.doc.createElement("div")),
										(a.className = "highcharts-tooltip-container"),
										u(a, {
											position: "absolute",
											top: "1px",
											pointerEvents: "none",
											zIndex: Math.max(
												this.options.style.zIndex || 0,
												((t && t.zIndex) || 0) + 3
											),
										}),
										(this.renderer = n =
											new i(a, 0, 0, t, void 0, void 0, n.styledMode));
								}
								if (
									(o
										? (this.label = n.g("tooltip"))
										: ((this.label = n
												.label(
													"",
													0,
													0,
													r.shape,
													void 0,
													void 0,
													r.useHTML,
													void 0,
													"tooltip"
												)
												.attr({ padding: r.padding, r: r.borderRadius })),
										  i ||
												this.label
													.attr({
														fill: r.backgroundColor,
														"stroke-width": r.borderWidth || 0,
													})
													.css(r.style)
													.css({
														pointerEvents:
															r.style.pointerEvents ||
															(this.shouldStickOnContact() ? "auto" : "none"),
													})),
									t.outside)
								) {
									let e = this.label,
										{ xSetter: i, ySetter: s } = e;
									(e.xSetter = function (s) {
										i.call(e, t.distance), a && (a.style.left = s + "px");
									}),
										(e.ySetter = function (i) {
											s.call(e, t.distance), a && (a.style.top = i + "px");
										});
								}
								this.label.attr({ zIndex: 8 }).shadow(r.shadow).add();
							}
							return (
								a && !a.parentElement && e.doc.body.appendChild(a), this.label
							);
						}
						getPlayingField() {
							let { body: t, documentElement: e } = h,
								{ chart: i, distance: s, outside: r } = this;
							return {
								width: r
									? Math.max(
											t.scrollWidth,
											e.scrollWidth,
											t.offsetWidth,
											e.offsetWidth,
											e.clientWidth
									  ) -
									  2 * s
									: i.chartWidth,
								height: r
									? Math.max(
											t.scrollHeight,
											e.scrollHeight,
											t.offsetHeight,
											e.offsetHeight,
											e.clientHeight
									  )
									: i.chartHeight,
							};
						}
						getPosition(t, e, i) {
							let { distance: s, chart: r, outside: o, pointer: a } = this,
								{ inverted: n, plotLeft: h, plotTop: l, polar: d } = r,
								{ plotX: c = 0, plotY: p = 0 } = i,
								u = {},
								g = (n && i.h) || 0,
								{ height: f, width: m } = this.getPlayingField(),
								x = a.getChartPosition(),
								y = (t) => t * x.scaleX,
								b = (t) => t * x.scaleY,
								v = (i) => {
									let a = "x" === i;
									return [i, a ? m : f, a ? t : e].concat(
										o
											? [
													a ? y(t) : b(e),
													a ? x.left - s + y(c + h) : x.top - s + b(p + l),
													0,
													a ? m : f,
											  ]
											: [
													a ? t : e,
													a ? c + h : p + l,
													a ? h : l,
													a ? h + r.plotWidth : l + r.plotHeight,
											  ]
									);
								},
								k = v("y"),
								C = v("x"),
								M,
								w = !!i.negative;
							!d && r.hoverSeries?.yAxis?.reversed && (w = !w);
							let T = !this.followPointer && S(i.ttBelow, !d && !n === w),
								A = function (t, e, i, r, a, n, h) {
									let l = o ? ("y" === t ? b(s) : y(s)) : s,
										d = (i - r) / 2,
										c = r < a - s,
										p = a + s + r < e,
										f = a - l - i + d,
										m = a + l - d;
									if (T && p) u[t] = m;
									else if (!T && c) u[t] = f;
									else if (c) u[t] = Math.min(h - r, f - g < 0 ? f : f - g);
									else {
										if (!p) return !1;
										u[t] = Math.max(n, m + g + i > e ? m : m + g);
									}
								},
								P = function (t, e, i, r, o) {
									if (o < s || o > e - s) return !1;
									o < i / 2
										? (u[t] = 1)
										: o > e - r / 2
										? (u[t] = e - r - 2)
										: (u[t] = o - i / 2);
								},
								L = function (t) {
									([k, C] = [C, k]), (M = t);
								},
								O = () => {
									!1 !== A.apply(0, k)
										? !1 !== P.apply(0, C) || M || (L(!0), O())
										: M
										? (u.x = u.y = 0)
										: (L(!0), O());
								};
							return ((n && !d) || this.len > 1) && L(), O(), u;
						}
						hide(t) {
							let e = this;
							r.clearTimeout(this.hideTimer),
								(t = S(t, this.options.hideDelay)),
								this.isHidden ||
									(this.hideTimer = M(function () {
										let i = e.getLabel();
										e.getLabel().animate(
											{ opacity: 0 },
											{
												duration: t ? 150 : t,
												complete: () => {
													i.hide(), e.container && e.container.remove();
												},
											}
										),
											(e.isHidden = !0);
									}, t));
						}
						init(t, e) {
							(this.chart = t),
								(this.options = e),
								(this.crosshairs = []),
								(this.now = { x: 0, y: 0 }),
								(this.isHidden = !0),
								(this.split = e.split && !t.inverted && !t.polar),
								(this.shared = e.shared || this.split),
								(this.outside = S(
									e.outside,
									!!(t.scrollablePixelsX || t.scrollablePixelsY)
								));
						}
						shouldStickOnContact(t) {
							return !!(
								!this.followPointer &&
								this.options.stickOnContact &&
								(!t || this.pointer.inClass(t.target, "highcharts-tooltip"))
							);
						}
						move(t, e, i, s) {
							let o = this,
								a = o.now,
								n =
									!1 !== o.options.animation &&
									!o.isHidden &&
									(Math.abs(t - a.x) > 1 || Math.abs(e - a.y) > 1),
								h = o.followPointer || o.len > 1;
							f(a, {
								x: n ? (2 * a.x + t) / 3 : t,
								y: n ? (a.y + e) / 2 : e,
								anchorX: h ? void 0 : n ? (2 * a.anchorX + i) / 3 : i,
								anchorY: h ? void 0 : n ? (a.anchorY + s) / 2 : s,
							}),
								o.getLabel().attr(a),
								o.drawTracker(),
								n &&
									(r.clearTimeout(this.tooltipTimeout),
									(this.tooltipTimeout = setTimeout(function () {
										o && o.move(t, e, i, s);
									}, 32)));
						}
						refresh(t, e) {
							let { chart: i, options: s, pointer: o, shared: n } = this,
								h = C(t),
								l = h[0],
								d = [],
								c = s.format,
								p = s.formatter || this.defaultFormatter,
								u = i.styledMode,
								g = {};
							if (!s.enabled || !l.series) return;
							r.clearTimeout(this.hideTimer),
								(this.allowShared = !(
									!x(t) &&
									t.series &&
									t.series.noSharedTooltip
								)),
								(this.followPointer =
									!this.split && l.series.tooltipOptions.followPointer);
							let f = this.getAnchor(t, e),
								y = f[0],
								v = f[1];
							n && this.allowShared
								? (o.applyInactiveState(h),
								  h.forEach(function (t) {
										t.setState("hover"), d.push(t.getLabelConfig());
								  }),
								  ((g = l.getLabelConfig()).points = d))
								: (g = l.getLabelConfig()),
								(this.len = d.length);
							let k = b(c) ? a(c, g, i) : p.call(g, this),
								M = l.series;
							if (
								((this.distance = S(M.tooltipOptions.distance, 16)), !1 === k)
							)
								this.hide();
							else {
								if (this.split && this.allowShared) this.renderSplit(k, h);
								else {
									let t = y,
										r = v;
									if (
										(e &&
											o.isDirectTouch &&
											((t = e.chartX - i.plotLeft), (r = e.chartY - i.plotTop)),
										i.polar ||
											!1 === M.options.clip ||
											h.some(
												(e) =>
													o.isDirectTouch || e.series.shouldShowTooltip(t, r)
											))
									) {
										let t = this.getLabel();
										(!s.style.width || u) &&
											t.css({
												width:
													(this.outside ? this.getPlayingField() : i.spacingBox)
														.width + "px",
											}),
											t.attr({ text: k && k.join ? k.join("") : k }),
											t.addClass(this.getClassName(l), !0),
											u ||
												t.attr({
													stroke:
														s.borderColor || l.color || M.color || "#666666",
												}),
											this.updatePosition({
												plotX: y,
												plotY: v,
												negative: l.negative,
												ttBelow: l.ttBelow,
												h: f[2] || 0,
											});
									} else {
										this.hide();
										return;
									}
								}
								this.isHidden &&
									this.label &&
									this.label.attr({ opacity: 1 }).show(),
									(this.isHidden = !1);
							}
							m(this, "refresh");
						}
						renderSplit(t, e) {
							let i = this,
								{
									chart: s,
									chart: {
										chartWidth: r,
										chartHeight: o,
										plotHeight: a,
										plotLeft: n,
										plotTop: c,
										scrollablePixelsY: u = 0,
										scrollablePixelsX: g,
										styledMode: m,
									},
									distance: x,
									options: y,
									options: { positioner: v },
									pointer: k,
								} = i,
								{ scrollLeft: C = 0, scrollTop: M = 0 } =
									s.scrollablePlotArea?.scrollingContainer || {},
								w =
									i.outside && "number" != typeof g
										? h.documentElement.getBoundingClientRect()
										: { left: C, right: C + r, top: M, bottom: M + o },
								T = i.getLabel(),
								A = this.renderer || s.renderer,
								P = !!(s.xAxis[0] && s.xAxis[0].opposite),
								{ left: L, top: O } = k.getChartPosition(),
								D = c + M,
								E = 0,
								I = a - u;
							function j(t, e, s, r, o = !0) {
								let a, n;
								return (
									s
										? ((a = P ? 0 : I),
										  (n = p(
												t - r / 2,
												w.left,
												w.right - r - (i.outside ? L : 0)
										  )))
										: ((a = e - D),
										  (n = p(
												(n = o ? t - r - x : t + x),
												o ? n : w.left,
												w.right
										  ))),
									{ x: n, y: a }
								);
							}
							b(t) && (t = [!1, t]);
							let B = t.slice(0, e.length + 1).reduce(function (t, s, r) {
								if (!1 !== s && "" !== s) {
									let o = e[r - 1] || {
											isHeader: !0,
											plotX: e[0].plotX,
											plotY: a,
											series: {},
										},
										h = o.isHeader,
										l = h ? i : o.series,
										d = (l.tt = (function (t, e, s) {
											let r = t,
												{ isHeader: o, series: a } = e;
											if (!r) {
												let t = { padding: y.padding, r: y.borderRadius };
												m ||
													((t.fill = y.backgroundColor),
													(t["stroke-width"] = y.borderWidth ?? 1)),
													(r = A.label(
														"",
														0,
														0,
														y[o ? "headerShape" : "shape"],
														void 0,
														void 0,
														y.useHTML
													)
														.addClass(i.getClassName(e, !0, o))
														.attr(t)
														.add(T));
											}
											return (
												(r.isActive = !0),
												r.attr({ text: s }),
												m ||
													r
														.css(y.style)
														.attr({
															stroke:
																y.borderColor ||
																e.color ||
																a.color ||
																"#333333",
														}),
												r
											);
										})(l.tt, o, s.toString())),
										u = d.getBBox(),
										g = u.width + d.strokeWidth();
									h && ((E = u.height), (I += E), P && (D -= E));
									let { anchorX: f, anchorY: b } = (function (t) {
										let e, i;
										let {
											isHeader: s,
											plotX: r = 0,
											plotY: o = 0,
											series: h,
										} = t;
										if (s) (e = Math.max(n + r, n)), (i = c + a / 2);
										else {
											let { xAxis: t, yAxis: s } = h;
											(e = t.pos + p(r, -x, t.len + x)),
												h.shouldShowTooltip(0, s.pos - c + o, {
													ignoreX: !0,
												}) && (i = s.pos + o);
										}
										return {
											anchorX: (e = p(e, w.left - x, w.right + x)),
											anchorY: i,
										};
									})(o);
									if ("number" == typeof b) {
										let e = u.height + 1,
											s = v ? v.call(i, g, e, o) : j(f, b, h, g);
										t.push({
											align: v ? 0 : void 0,
											anchorX: f,
											anchorY: b,
											boxWidth: g,
											point: o,
											rank: S(s.rank, h ? 1 : 0),
											size: e,
											target: s.y,
											tt: d,
											x: s.x,
										});
									} else d.isActive = !1;
								}
								return t;
							}, []);
							!v &&
								B.some((t) => {
									let { outside: e } = i,
										s = (e ? L : 0) + t.anchorX;
									return (
										(s < w.left && s + t.boxWidth < w.right) ||
										(s < L - w.left + t.boxWidth && w.right - s > s)
									);
								}) &&
								(B = B.map((t) => {
									let { x: e, y: i } = j(
										t.anchorX,
										t.anchorY,
										t.point.isHeader,
										t.boxWidth,
										!1
									);
									return f(t, { target: i, x: e });
								})),
								i.cleanSplit(),
								d(B, I);
							let R = { left: L, right: L };
							B.forEach(function (t) {
								let { x: e, boxWidth: s, isHeader: r } = t;
								!r &&
									(i.outside && L + e < R.left && (R.left = L + e),
									!r && i.outside && R.left + s > R.right && (R.right = L + e));
							}),
								B.forEach(function (t) {
									let {
											x: e,
											anchorX: s,
											anchorY: r,
											pos: o,
											point: { isHeader: a },
										} = t,
										n = {
											visibility: void 0 === o ? "hidden" : "inherit",
											x: e,
											y: (o || 0) + D,
											anchorX: s,
											anchorY: r,
										};
									if (i.outside && e < s) {
										let t = L - R.left;
										t > 0 &&
											(a || ((n.x = e + t), (n.anchorX = s + t)),
											a &&
												((n.x = (R.right - R.left) / 2), (n.anchorX = s + t)));
									}
									t.tt.attr(n);
								});
							let { container: z, outside: N, renderer: W } = i;
							if (N && z && W) {
								let { width: t, height: e, x: i, y: s } = T.getBBox();
								W.setSize(t + i, e + s, !1),
									(z.style.left = R.left + "px"),
									(z.style.top = O + "px");
							}
							l && T.attr({ opacity: 1 === T.opacity ? 0.999 : 1 });
						}
						drawTracker() {
							if (!this.shouldStickOnContact()) {
								this.tracker && (this.tracker = this.tracker.destroy());
								return;
							}
							let t = this.chart,
								e = this.label,
								i = this.shared ? t.hoverPoints : t.hoverPoint;
							if (!e || !i) return;
							let s = { x: 0, y: 0, width: 0, height: 0 },
								r = this.getAnchor(i),
								o = e.getBBox();
							(r[0] += t.plotLeft - (e.translateX || 0)),
								(r[1] += t.plotTop - (e.translateY || 0)),
								(s.x = Math.min(0, r[0])),
								(s.y = Math.min(0, r[1])),
								(s.width =
									r[0] < 0
										? Math.max(Math.abs(r[0]), o.width - r[0])
										: Math.max(Math.abs(r[0]), o.width)),
								(s.height =
									r[1] < 0
										? Math.max(Math.abs(r[1]), o.height - Math.abs(r[1]))
										: Math.max(Math.abs(r[1]), o.height)),
								this.tracker
									? this.tracker.attr(s)
									: ((this.tracker = e.renderer
											.rect(s)
											.addClass("highcharts-tracker")
											.add(e)),
									  t.styledMode ||
											this.tracker.attr({ fill: "rgba(0,0,0,0)" }));
						}
						styledModeFormat(t) {
							return t
								.replace(
									'style="font-size: 0.8em"',
									'class="highcharts-header"'
								)
								.replace(
									/style="color:{(point|series)\.color}"/g,
									'class="highcharts-color-{$1.colorIndex} {series.options.className} {point.options.className}"'
								);
						}
						tooltipFooterHeaderFormatter(t, e) {
							let i = t.series,
								s = i.tooltipOptions,
								r = i.xAxis,
								o = r && r.dateTime,
								n = { isFooter: e, labelConfig: t },
								h = s.xDateFormat,
								l = s[e ? "footerFormat" : "headerFormat"];
							return (
								m(this, "headerFormatter", n, function (e) {
									o &&
										!h &&
										y(t.key) &&
										(h = o.getXDateFormat(t.key, s.dateTimeLabelFormats)),
										o &&
											h &&
											((t.point && t.point.tooltipDateKeys) || ["key"]).forEach(
												function (t) {
													l = l.replace(
														"{point." + t + "}",
														"{point." + t + ":" + h + "}"
													);
												}
											),
										i.chart.styledMode && (l = this.styledModeFormat(l)),
										(e.text = a(l, { point: t, series: i }, this.chart));
								}),
								n.text
							);
						}
						update(t) {
							this.destroy(), this.init(this.chart, v(!0, this.options, t));
						}
						updatePosition(t) {
							let {
									chart: e,
									container: i,
									distance: s,
									options: r,
									pointer: o,
									renderer: a,
								} = this,
								{ height: n = 0, width: h = 0 } = this.getLabel(),
								{
									left: l,
									top: d,
									scaleX: c,
									scaleY: p,
								} = o.getChartPosition(),
								g = (r.positioner || this.getPosition).call(this, h, n, t),
								f = (t.plotX || 0) + e.plotLeft,
								m = (t.plotY || 0) + e.plotTop,
								x;
							a &&
								i &&
								(r.positioner && ((g.x += l - s), (g.y += d - s)),
								(x = (r.borderWidth || 0) + 2 * s + 2),
								a.setSize(h + x, n + x, !1),
								(1 !== c || 1 !== p) &&
									(u(i, { transform: `scale(${c}, ${p})` }),
									(f *= c),
									(m *= p)),
								(f += l - g.x),
								(m += d - g.y)),
								this.move(Math.round(g.x), Math.round(g.y || 0), f, m);
						}
					}
					return (
						((o = w || (w = {})).compose = function (t) {
							k(n, "Core.Tooltip") &&
								c(t, "afterInit", function () {
									let t = this.chart;
									t.options.tooltip &&
										(t.tooltip = new o(t, t.options.tooltip, this));
								});
						}),
						w
					);
				}
			),
			i(
				e,
				"Core/Series/Point.js",
				[
					e["Core/Renderer/HTML/AST.js"],
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Defaults.js"],
					e["Core/Templating.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r) {
					let { animObject: o } = e,
						{ defaultOptions: a } = i,
						{ format: n } = s,
						{
							addEvent: h,
							erase: l,
							extend: d,
							fireEvent: c,
							getNestedProperty: p,
							isArray: u,
							isFunction: g,
							isNumber: f,
							isObject: m,
							pick: x,
							syncTimeout: y,
							removeEvent: b,
							uniqueKey: v,
						} = r;
					class S {
						animateBeforeDestroy() {
							let t = this,
								e = { x: t.startXPos, opacity: 0 },
								i = t.getGraphicalProps();
							i.singular.forEach(function (i) {
								t[i] = t[i].animate(
									"dataLabel" === i
										? { x: t[i].startXPos, y: t[i].startYPos, opacity: 0 }
										: e
								);
							}),
								i.plural.forEach(function (e) {
									t[e].forEach(function (e) {
										e.element &&
											e.animate(
												d(
													{ x: t.startXPos },
													e.startYPos ? { x: e.startXPos, y: e.startYPos } : {}
												)
											);
									});
								});
						}
						applyOptions(t, e) {
							let i = this.series,
								s = i.options.pointValKey || i.pointValKey;
							return (
								d(this, (t = S.prototype.optionsToObject.call(this, t))),
								(this.options = this.options ? d(this.options, t) : t),
								t.group && delete this.group,
								t.dataLabels && delete this.dataLabels,
								s && (this.y = S.prototype.getNestedProperty.call(this, s)),
								this.selected && (this.state = "select"),
								"name" in this &&
									void 0 === e &&
									i.xAxis &&
									i.xAxis.hasNames &&
									(this.x = i.xAxis.nameToX(this)),
								void 0 === this.x && i
									? void 0 === e
										? (this.x = i.autoIncrement())
										: (this.x = e)
									: f(t.x) &&
									  i.options.relativeXValue &&
									  (this.x = i.autoIncrement(t.x)),
								(this.isNull = this.isValid && !this.isValid()),
								(this.formatPrefix = this.isNull ? "null" : "point"),
								this
							);
						}
						destroy() {
							if (!this.destroyed) {
								let t = this,
									e = t.series,
									i = e.chart,
									s = e.options.dataSorting,
									r = i.hoverPoints,
									a = o(t.series.chart.renderer.globalAnimation),
									n = () => {
										for (let e in ((t.graphic ||
											t.graphics ||
											t.dataLabel ||
											t.dataLabels) &&
											(b(t), t.destroyElements()),
										t))
											delete t[e];
									};
								t.legendItem && i.legend.destroyItem(t),
									r &&
										(t.setState(), l(r, t), r.length || (i.hoverPoints = null)),
									t === i.hoverPoint && t.onMouseOut(),
									s && s.enabled
										? (this.animateBeforeDestroy(), y(n, a.duration))
										: n(),
									i.pointCount--;
							}
							this.destroyed = !0;
						}
						destroyElements(t) {
							let e = this,
								i = e.getGraphicalProps(t);
							i.singular.forEach(function (t) {
								e[t] = e[t].destroy();
							}),
								i.plural.forEach(function (t) {
									e[t].forEach(function (t) {
										t && t.element && t.destroy();
									}),
										delete e[t];
								});
						}
						firePointEvent(t, e, i) {
							let s = this,
								r = this.series.options;
							s.manageEvent(t),
								"click" === t &&
									r.allowPointSelect &&
									(i = function (t) {
										!s.destroyed &&
											s.select &&
											s.select(null, t.ctrlKey || t.metaKey || t.shiftKey);
									}),
								c(s, t, e, i);
						}
						getClassName() {
							return (
								"highcharts-point" +
								(this.selected ? " highcharts-point-select" : "") +
								(this.negative ? " highcharts-negative" : "") +
								(this.isNull ? " highcharts-null-point" : "") +
								(void 0 !== this.colorIndex
									? " highcharts-color-" + this.colorIndex
									: "") +
								(this.options.className ? " " + this.options.className : "") +
								(this.zone && this.zone.className
									? " " + this.zone.className.replace("highcharts-negative", "")
									: "")
							);
						}
						getGraphicalProps(t) {
							let e, i;
							let s = this,
								r = [],
								o = { singular: [], plural: [] };
							for (
								(t = t || { graphic: 1, dataLabel: 1 }).graphic &&
									r.push("graphic", "connector"),
									t.dataLabel &&
										r.push("dataLabel", "dataLabelPath", "dataLabelUpper"),
									i = r.length;
								i--;

							)
								s[(e = r[i])] && o.singular.push(e);
							return (
								["graphic", "dataLabel"].forEach(function (e) {
									let i = e + "s";
									t[e] && s[i] && o.plural.push(i);
								}),
								o
							);
						}
						getLabelConfig() {
							return {
								x: this.category,
								y: this.y,
								color: this.color,
								colorIndex: this.colorIndex,
								key: this.name || this.category,
								series: this.series,
								point: this,
								percentage: this.percentage,
								total: this.total || this.stackTotal,
							};
						}
						getNestedProperty(t) {
							return t
								? 0 === t.indexOf("custom.")
									? p(t, this.options)
									: this[t]
								: void 0;
						}
						getZone() {
							let t = this.series,
								e = t.zones,
								i = t.zoneAxis || "y",
								s,
								r = 0;
							for (s = e[0]; this[i] >= s.value; ) s = e[++r];
							return (
								this.nonZonedColor || (this.nonZonedColor = this.color),
								s && s.color && !this.options.color
									? (this.color = s.color)
									: (this.color = this.nonZonedColor),
								s
							);
						}
						hasNewShapeType() {
							return (
								(this.graphic &&
									(this.graphic.symbolName ||
										this.graphic.element.nodeName)) !== this.shapeType
							);
						}
						constructor(t, e, i) {
							(this.formatPrefix = "point"),
								(this.visible = !0),
								(this.series = t),
								this.applyOptions(e, i),
								this.id ?? (this.id = v()),
								this.resolveColor(),
								t.chart.pointCount++,
								c(this, "afterInit");
						}
						isValid() {
							return (f(this.x) || this.x instanceof Date) && f(this.y);
						}
						optionsToObject(t) {
							let e = this.series,
								i = e.options.keys,
								s = i || e.pointArrayMap || ["y"],
								r = s.length,
								o = {},
								a,
								n = 0,
								h = 0;
							if (f(t) || null === t) o[s[0]] = t;
							else if (u(t))
								for (
									!i &&
									t.length > r &&
									("string" == (a = typeof t[0])
										? (o.name = t[0])
										: "number" === a && (o.x = t[0]),
									n++);
									h < r;

								)
									(i && void 0 === t[n]) ||
										(s[h].indexOf(".") > 0
											? S.prototype.setNestedProperty(o, t[n], s[h])
											: (o[s[h]] = t[n])),
										n++,
										h++;
							else
								"object" == typeof t &&
									((o = t),
									t.dataLabels && (e.hasDataLabels = () => !0),
									t.marker && (e._hasPointMarkers = !0));
							return o;
						}
						pos(t, e = this.plotY) {
							if (!this.destroyed) {
								let { plotX: i, series: s } = this,
									{ chart: r, xAxis: o, yAxis: a } = s,
									n = 0,
									h = 0;
								if (f(i) && f(e))
									return (
										t &&
											((n = o ? o.pos : r.plotLeft),
											(h = a ? a.pos : r.plotTop)),
										r.inverted && o && a
											? [a.len - e + h, o.len - i + n]
											: [i + n, e + h]
									);
							}
						}
						resolveColor() {
							let t = this.series,
								e = t.chart.options.chart,
								i = t.chart.styledMode,
								s,
								r,
								o = e.colorCount,
								a;
							delete this.nonZonedColor,
								t.options.colorByPoint
									? (i ||
											((s = (r = t.options.colors || t.chart.options.colors)[
												t.colorCounter
											]),
											(o = r.length)),
									  (a = t.colorCounter),
									  t.colorCounter++,
									  t.colorCounter === o && (t.colorCounter = 0))
									: (i || (s = t.color), (a = t.colorIndex)),
								(this.colorIndex = x(this.options.colorIndex, a)),
								(this.color = x(this.options.color, s));
						}
						setNestedProperty(t, e, i) {
							return (
								i.split(".").reduce(function (t, i, s, r) {
									let o = r.length - 1 === s;
									return (t[i] = o ? e : m(t[i], !0) ? t[i] : {}), t[i];
								}, t),
								t
							);
						}
						shouldDraw() {
							return !this.isNull;
						}
						tooltipFormatter(t) {
							let e = this.series,
								i = e.tooltipOptions,
								s = x(i.valueDecimals, ""),
								r = i.valuePrefix || "",
								o = i.valueSuffix || "";
							return (
								e.chart.styledMode && (t = e.chart.tooltip.styledModeFormat(t)),
								(e.pointArrayMap || ["y"]).forEach(function (e) {
									(e = "{point." + e),
										(r || o) &&
											(t = t.replace(RegExp(e + "}", "g"), r + e + "}" + o)),
										(t = t.replace(RegExp(e + "}", "g"), e + ":,." + s + "f}"));
								}),
								n(t, { point: this, series: this.series }, e.chart)
							);
						}
						update(t, e, i, s) {
							let r;
							let o = this,
								a = o.series,
								n = o.graphic,
								h = a.chart,
								l = a.options;
							function d() {
								o.applyOptions(t);
								let s = n && o.hasMockGraphic,
									d = null === o.y ? !s : s;
								n && d && ((o.graphic = n.destroy()), delete o.hasMockGraphic),
									m(t, !0) &&
										(n &&
											n.element &&
											t &&
											t.marker &&
											void 0 !== t.marker.symbol &&
											(o.graphic = n.destroy()),
										t?.dataLabels &&
											o.dataLabel &&
											(o.dataLabel = o.dataLabel.destroy())),
									(r = o.index),
									a.updateParallelArrays(o, r),
									(l.data[r] =
										m(l.data[r], !0) || m(t, !0) ? o.options : x(t, l.data[r])),
									(a.isDirty = a.isDirtyData = !0),
									!a.fixedBox && a.hasCartesianSeries && (h.isDirtyBox = !0),
									"point" === l.legendType && (h.isDirtyLegend = !0),
									e && h.redraw(i);
							}
							(e = x(e, !0)),
								!1 === s ? d() : o.firePointEvent("update", { options: t }, d);
						}
						remove(t, e) {
							this.series.removePoint(this.series.data.indexOf(this), t, e);
						}
						select(t, e) {
							let i = this,
								s = i.series,
								r = s.chart;
							(t = x(t, !i.selected)),
								(this.selectedStaging = t),
								i.firePointEvent(
									t ? "select" : "unselect",
									{ accumulate: e },
									function () {
										(i.selected = i.options.selected = t),
											(s.options.data[s.data.indexOf(i)] = i.options),
											i.setState(t && "select"),
											e ||
												r.getSelectedPoints().forEach(function (t) {
													let e = t.series;
													t.selected &&
														t !== i &&
														((t.selected = t.options.selected = !1),
														(e.options.data[e.data.indexOf(t)] = t.options),
														t.setState(
															r.hoverPoints && e.options.inactiveOtherPoints
																? "inactive"
																: ""
														),
														t.firePointEvent("unselect"));
												});
									}
								),
								delete this.selectedStaging;
						}
						onMouseOver(t) {
							let { inverted: e, pointer: i } = this.series.chart;
							i &&
								((t = t
									? i.normalize(t)
									: i.getChartCoordinatesFromPoint(this, e)),
								i.runPointActions(t, this));
						}
						onMouseOut() {
							let t = this.series.chart;
							this.firePointEvent("mouseOut"),
								this.series.options.inactiveOtherPoints ||
									(t.hoverPoints || []).forEach(function (t) {
										t.setState();
									}),
								(t.hoverPoints = t.hoverPoint = null);
						}
						manageEvent(t) {
							let e = this.series.options.point || {},
								i = e.events?.[t];
							g(i) &&
							(!this.hcEvents?.[t] ||
								this.hcEvents?.[t]?.map((t) => t.fn).indexOf(i) === -1)
								? (h(this, t, i), (this.hasImportedEvents = !0))
								: this.hasImportedEvents &&
								  !i &&
								  this.hcEvents?.[t] &&
								  (b(this, t),
								  delete this.hcEvents[t],
								  Object.keys(this.hcEvents) || (this.hasImportedEvents = !1));
						}
						setState(e, i) {
							let s = this.series,
								r = this.state,
								o = s.options.states[e || "normal"] || {},
								n = a.plotOptions[s.type].marker && s.options.marker,
								h = n && !1 === n.enabled,
								l = (n && n.states && n.states[e || "normal"]) || {},
								p = !1 === l.enabled,
								u = this.marker || {},
								g = s.chart,
								m = n && s.markerAttribs,
								y = s.halo,
								b,
								v,
								S,
								k = s.stateMarkerGraphic,
								C;
							if (
								((e = e || "") === this.state && !i) ||
								(this.selected && "select" !== e) ||
								!1 === o.enabled ||
								(e && (p || (h && !1 === l.enabled))) ||
								(e && u.states && u.states[e] && !1 === u.states[e].enabled)
							)
								return;
							if (
								((this.state = e),
								m && (b = s.markerAttribs(this, e)),
								this.graphic && !this.hasMockGraphic)
							) {
								if (
									(r && this.graphic.removeClass("highcharts-point-" + r),
									e && this.graphic.addClass("highcharts-point-" + e),
									!g.styledMode)
								) {
									(v = s.pointAttribs(this, e)),
										(S = x(g.options.chart.animation, o.animation));
									let t = v.opacity;
									s.options.inactiveOtherPoints &&
										f(t) &&
										(this.dataLabels || []).forEach(function (e) {
											e &&
												!e.hasClass("highcharts-data-label-hidden") &&
												(e.animate({ opacity: t }, S),
												e.connector && e.connector.animate({ opacity: t }, S));
										}),
										this.graphic.animate(v, S);
								}
								b &&
									this.graphic.animate(
										b,
										x(g.options.chart.animation, l.animation, n.animation)
									),
									k && k.hide();
							} else
								e &&
									l &&
									((C = u.symbol || s.symbol),
									k && k.currentSymbol !== C && (k = k.destroy()),
									b &&
										(k
											? k[i ? "animate" : "attr"]({ x: b.x, y: b.y })
											: C &&
											  ((s.stateMarkerGraphic = k =
													g.renderer
														.symbol(C, b.x, b.y, b.width, b.height)
														.add(s.markerGroup)),
											  (k.currentSymbol = C))),
									!g.styledMode &&
										k &&
										"inactive" !== this.state &&
										k.attr(s.pointAttribs(this, e))),
									k &&
										(k[e && this.isInside ? "show" : "hide"](),
										(k.element.point = this),
										k.addClass(this.getClassName(), !0));
							let M = o.halo,
								w = this.graphic || k,
								T = (w && w.visibility) || "inherit";
							M && M.size && w && "hidden" !== T && !this.isCluster
								? (y || (s.halo = y = g.renderer.path().add(w.parentGroup)),
								  y
										.show()
										[i ? "animate" : "attr"]({ d: this.haloPath(M.size) }),
								  y.attr({
										class:
											"highcharts-halo highcharts-color-" +
											x(this.colorIndex, s.colorIndex) +
											(this.className ? " " + this.className : ""),
										visibility: T,
										zIndex: -1,
								  }),
								  (y.point = this),
								  g.styledMode ||
										y.attr(
											d(
												{
													fill: this.color || s.color,
													"fill-opacity": M.opacity,
												},
												t.filterUserAttributes(M.attributes || {})
											)
										))
								: y &&
								  y.point &&
								  y.point.haloPath &&
								  y.animate({ d: y.point.haloPath(0) }, null, y.hide),
								c(this, "afterSetState", { state: e });
						}
						haloPath(t) {
							let e = this.pos();
							return e
								? this.series.chart.renderer.symbols.circle(
										Math.floor(e[0]) - t,
										e[1] - t,
										2 * t,
										2 * t
								  )
								: [];
						}
					}
					return S;
				}
			),
			i(
				e,
				"Core/Pointer.js",
				[
					e["Core/Color/Color.js"],
					e["Core/Globals.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					var s;
					let { parse: r } = t,
						{ charts: o, composed: a } = e,
						{
							addEvent: n,
							attr: h,
							css: l,
							extend: d,
							find: c,
							fireEvent: p,
							isNumber: u,
							isObject: g,
							objectEach: f,
							offset: m,
							pick: x,
							pushUnique: y,
							splat: b,
						} = i;
					class v {
						applyInactiveState(t) {
							let e = [],
								i;
							(t || []).forEach(function (t) {
								(i = t.series),
									e.push(i),
									i.linkedParent && e.push(i.linkedParent),
									i.linkedSeries && (e = e.concat(i.linkedSeries)),
									i.navigatorSeries && e.push(i.navigatorSeries);
							}),
								this.chart.series.forEach(function (t) {
									-1 === e.indexOf(t)
										? t.setState("inactive", !0)
										: t.options.inactiveOtherPoints &&
										  t.setAllPointsToState("inactive");
								});
						}
						destroy() {
							let t = this;
							this.eventsToUnbind.forEach((t) => t()),
								(this.eventsToUnbind = []),
								!e.chartCount &&
									(v.unbindDocumentMouseUp &&
										(v.unbindDocumentMouseUp = v.unbindDocumentMouseUp()),
									v.unbindDocumentTouchEnd &&
										(v.unbindDocumentTouchEnd = v.unbindDocumentTouchEnd())),
								clearInterval(t.tooltipTimeout),
								f(t, function (e, i) {
									t[i] = void 0;
								});
						}
						getSelectionMarkerAttrs(t, e) {
							let i = {
								args: { chartX: t, chartY: e },
								attrs: {},
								shapeType: "rect",
							};
							return (
								p(this, "getSelectionMarkerAttrs", i, (i) => {
									let s;
									let { chart: r, zoomHor: o, zoomVert: a } = this,
										{ mouseDownX: n = 0, mouseDownY: h = 0 } = r,
										l = i.attrs;
									(l.x = r.plotLeft),
										(l.y = r.plotTop),
										(l.width = o ? 1 : r.plotWidth),
										(l.height = a ? 1 : r.plotHeight),
										o &&
											((s = t - n),
											(l.width = Math.max(1, Math.abs(s))),
											(l.x = (s > 0 ? 0 : s) + n)),
										a &&
											((s = e - h),
											(l.height = Math.max(1, Math.abs(s))),
											(l.y = (s > 0 ? 0 : s) + h));
								}),
								i
							);
						}
						drag(t) {
							let { chart: e } = this,
								{ mouseDownX: i = 0, mouseDownY: s = 0 } = e,
								{
									panning: o,
									panKey: a,
									selectionMarkerFill: n,
								} = e.options.chart,
								h = e.plotLeft,
								l = e.plotTop,
								d = e.plotWidth,
								c = e.plotHeight,
								p = g(o) ? o.enabled : o,
								u = a && t[`${a}Key`],
								f = t.chartX,
								m = t.chartY,
								x,
								y = this.selectionMarker;
							if (
								(!y || !y.touch) &&
								(f < h ? (f = h) : f > h + d && (f = h + d),
								m < l ? (m = l) : m > l + c && (m = l + c),
								(this.hasDragged = Math.sqrt(
									Math.pow(i - f, 2) + Math.pow(s - m, 2)
								)),
								this.hasDragged > 10)
							) {
								x = e.isInsidePlot(i - h, s - l, { visiblePlotOnly: !0 });
								let { shapeType: a, attrs: d } = this.getSelectionMarkerAttrs(
									f,
									m
								);
								(e.hasCartesianSeries || e.mapView) &&
									this.hasZoom &&
									x &&
									!u &&
									!y &&
									((this.selectionMarker = y = e.renderer[a]()),
									y
										.attr({ class: "highcharts-selection-marker", zIndex: 7 })
										.add(),
									e.styledMode ||
										y.attr({ fill: n || r("#334eff").setOpacity(0.25).get() })),
									y && y.attr(d),
									x && !y && p && e.pan(t, o);
							}
						}
						dragStart(t) {
							let e = this.chart;
							(e.mouseIsDown = t.type),
								(e.cancelClick = !1),
								(e.mouseDownX = t.chartX),
								(e.mouseDownY = t.chartY);
						}
						getSelectionBox(t) {
							let e = { args: { marker: t }, result: t.getBBox() };
							return p(this, "getSelectionBox", e), e.result;
						}
						drop(t) {
							let e;
							let { chart: i, selectionMarker: s } = this;
							for (let t of i.axes)
								t.isPanning &&
									((t.isPanning = !1),
									(t.options.startOnTick ||
										t.options.endOnTick ||
										t.series.some((t) => t.boosted)) &&
										((t.forceRedraw = !0),
										t.setExtremes(t.userMin, t.userMax, !1),
										(e = !0)));
							if ((e && i.redraw(), s && t)) {
								if (this.hasDragged) {
									let e = this.getSelectionBox(s);
									i.transform({
										axes: i.axes.filter(
											(t) =>
												t.zoomEnabled &&
												(("xAxis" === t.coll && this.zoomX) ||
													("yAxis" === t.coll && this.zoomY))
										),
										selection: { originalEvent: t, xAxis: [], yAxis: [], ...e },
										from: e,
									});
								}
								u(i.index) && (this.selectionMarker = s.destroy());
							}
							i &&
								u(i.index) &&
								(l(i.container, { cursor: i._cursor }),
								(i.cancelClick = this.hasDragged > 10),
								(i.mouseIsDown = !1),
								(this.hasDragged = 0),
								(this.pinchDown = []));
						}
						findNearestKDPoint(t, e, i) {
							let s;
							return (
								t.forEach(function (t) {
									let r =
											!(t.noSharedTooltip && e) &&
											0 > t.options.findNearestPointBy.indexOf("y"),
										o = t.searchPoint(i, r);
									g(o, !0) &&
										o.series &&
										(!g(s, !0) ||
											(function (t, i) {
												let s = t.distX - i.distX,
													r = t.dist - i.dist,
													o = i.series.group?.zIndex - t.series.group?.zIndex;
												return 0 !== s && e
													? s
													: 0 !== r
													? r
													: 0 !== o
													? o
													: t.series.index > i.series.index
													? -1
													: 1;
											})(s, o) > 0) &&
										(s = o);
								}),
								s
							);
						}
						getChartCoordinatesFromPoint(t, e) {
							let { xAxis: i, yAxis: s } = t.series,
								r = t.shapeArgs;
							if (i && s) {
								let o = t.clientX ?? t.plotX ?? 0,
									a = t.plotY || 0;
								return (
									t.isNode && r && u(r.x) && u(r.y) && ((o = r.x), (a = r.y)),
									e
										? { chartX: s.len + s.pos - a, chartY: i.len + i.pos - o }
										: { chartX: o + i.pos, chartY: a + s.pos }
								);
							}
							if (r && r.x && r.y) return { chartX: r.x, chartY: r.y };
						}
						getChartPosition() {
							if (this.chartPosition) return this.chartPosition;
							let { container: t } = this.chart,
								e = m(t);
							this.chartPosition = {
								left: e.left,
								top: e.top,
								scaleX: 1,
								scaleY: 1,
							};
							let { offsetHeight: i, offsetWidth: s } = t;
							return (
								s > 2 &&
									i > 2 &&
									((this.chartPosition.scaleX = e.width / s),
									(this.chartPosition.scaleY = e.height / i)),
								this.chartPosition
							);
						}
						getCoordinates(t) {
							let e = { xAxis: [], yAxis: [] };
							for (let i of this.chart.axes)
								e[i.isXAxis ? "xAxis" : "yAxis"].push({
									axis: i,
									value: i.toValue(t[i.horiz ? "chartX" : "chartY"]),
								});
							return e;
						}
						getHoverData(t, e, i, s, r, o) {
							let a = [],
								n = function (t) {
									return (
										t.visible &&
										!(!r && t.directTouch) &&
										x(t.options.enableMouseTracking, !0)
									);
								},
								h = e,
								l,
								d = {
									chartX: o ? o.chartX : void 0,
									chartY: o ? o.chartY : void 0,
									shared: r,
								};
							p(this, "beforeGetHoverData", d),
								(l =
									h && !h.stickyTracking
										? [h]
										: i.filter((t) => t.stickyTracking && (d.filter || n)(t)));
							let u = (s && t) || !o ? t : this.findNearestKDPoint(l, r, o);
							return (
								(h = u && u.series),
								u &&
									(r && !h.noSharedTooltip
										? (l = i.filter(function (t) {
												return d.filter
													? d.filter(t)
													: n(t) && !t.noSharedTooltip;
										  })).forEach(function (t) {
												let e = c(t.points, function (t) {
													return t.x === u.x && !t.isNull;
												});
												g(e) &&
													(t.boosted && t.boost && (e = t.boost.getPoint(e)),
													a.push(e));
										  })
										: a.push(u)),
								p(this, "afterGetHoverData", (d = { hoverPoint: u })),
								{ hoverPoint: d.hoverPoint, hoverSeries: h, hoverPoints: a }
							);
						}
						getPointFromEvent(t) {
							let e = t.target,
								i;
							for (; e && !i; ) (i = e.point), (e = e.parentNode);
							return i;
						}
						onTrackerMouseOut(t) {
							let e = this.chart,
								i = t.relatedTarget,
								s = e.hoverSeries;
							(this.isDirectTouch = !1),
								!s ||
									!i ||
									s.stickyTracking ||
									this.inClass(i, "highcharts-tooltip") ||
									(this.inClass(i, "highcharts-series-" + s.index) &&
										this.inClass(i, "highcharts-tracker")) ||
									s.onMouseOut();
						}
						inClass(t, e) {
							let i = t,
								s;
							for (; i; ) {
								if ((s = h(i, "class"))) {
									if (-1 !== s.indexOf(e)) return !0;
									if (-1 !== s.indexOf("highcharts-container")) return !1;
								}
								i = i.parentElement;
							}
						}
						constructor(t, e) {
							(this.hasDragged = 0),
								(this.eventsToUnbind = []),
								(this.options = e),
								(this.chart = t),
								(this.runChartClick = !!e.chart.events?.click),
								(this.pinchDown = []),
								this.setDOMEvents(),
								p(this, "afterInit");
						}
						normalize(t, e) {
							let i = t.touches,
								s = i
									? i.length
										? i.item(0)
										: x(i.changedTouches, t.changedTouches)[0]
									: t;
							e || (e = this.getChartPosition());
							let r = s.pageX - e.left,
								o = s.pageY - e.top;
							return d(t, {
								chartX: Math.round((r /= e.scaleX)),
								chartY: Math.round((o /= e.scaleY)),
							});
						}
						onContainerClick(t) {
							let e = this.chart,
								i = e.hoverPoint,
								s = this.normalize(t),
								r = e.plotLeft,
								o = e.plotTop;
							!e.cancelClick &&
								(i && this.inClass(s.target, "highcharts-tracker")
									? (p(i.series, "click", d(s, { point: i })),
									  e.hoverPoint && i.firePointEvent("click", s))
									: (d(s, this.getCoordinates(s)),
									  e.isInsidePlot(s.chartX - r, s.chartY - o, {
											visiblePlotOnly: !0,
									  }) && p(e, "click", s)));
						}
						onContainerMouseDown(t) {
							let i = (1 & (t.buttons || t.button)) == 1;
							(t = this.normalize(t)),
								e.isFirefox && 0 !== t.button && this.onContainerMouseMove(t),
								(void 0 === t.button || i) &&
									(this.zoomOption(t),
									i && t.preventDefault?.(),
									this.dragStart(t));
						}
						onContainerMouseLeave(t) {
							let { pointer: e } = o[x(v.hoverChartIndex, -1)] || {};
							(t = this.normalize(t)),
								this.onContainerMouseMove(t),
								e &&
									t.relatedTarget &&
									!this.inClass(t.relatedTarget, "highcharts-tooltip") &&
									(e.reset(), (e.chartPosition = void 0));
						}
						onContainerMouseEnter() {
							delete this.chartPosition;
						}
						onContainerMouseMove(t) {
							let e = this.chart,
								i = e.tooltip,
								s = this.normalize(t);
							this.setHoverChartIndex(t),
								("mousedown" === e.mouseIsDown || this.touchSelect(s)) &&
									this.drag(s),
								!e.openMenu &&
									(this.inClass(s.target, "highcharts-tracker") ||
										e.isInsidePlot(
											s.chartX - e.plotLeft,
											s.chartY - e.plotTop,
											{ visiblePlotOnly: !0 }
										)) &&
									!(i && i.shouldStickOnContact(s)) &&
									(this.inClass(s.target, "highcharts-no-tooltip")
										? this.reset(!1, 0)
										: this.runPointActions(s));
						}
						onDocumentTouchEnd(t) {
							this.onDocumentMouseUp(t);
						}
						onContainerTouchMove(t) {
							this.touchSelect(t)
								? this.onContainerMouseMove(t)
								: this.touch(t);
						}
						onContainerTouchStart(t) {
							this.touchSelect(t)
								? this.onContainerMouseDown(t)
								: (this.zoomOption(t), this.touch(t, !0));
						}
						onDocumentMouseMove(t) {
							let e = this.chart,
								i = e.tooltip,
								s = this.chartPosition,
								r = this.normalize(t, s);
							!s ||
								e.isInsidePlot(r.chartX - e.plotLeft, r.chartY - e.plotTop, {
									visiblePlotOnly: !0,
								}) ||
								(i && i.shouldStickOnContact(r)) ||
								this.inClass(r.target, "highcharts-tracker") ||
								this.reset();
						}
						onDocumentMouseUp(t) {
							o[x(v.hoverChartIndex, -1)]?.pointer?.drop(t);
						}
						pinch(t) {
							let e = this,
								{ chart: i, hasZoom: s, lastTouches: r } = e,
								o = [].map.call(t.touches || [], (t) => e.normalize(t)),
								a = o.length,
								n =
									1 === a &&
									((e.inClass(t.target, "highcharts-tracker") &&
										i.runTrackerClick) ||
										e.runChartClick),
								h = i.tooltip,
								l = 1 === a && x(h?.options.followTouchMove, !0);
							a > 1 ? (e.initiated = !0) : l && (e.initiated = !1),
								s &&
									e.initiated &&
									!n &&
									!1 !== t.cancelable &&
									t.preventDefault(),
								"touchstart" === t.type
									? ((e.pinchDown = o), (e.res = !0))
									: l
									? this.runPointActions(e.normalize(t))
									: r &&
									  (p(i, "touchpan", { originalEvent: t, touches: o }, () => {
											let e = (t) => {
												let e = t[0],
													i = t[1] || e;
												return {
													x: e.chartX,
													y: e.chartY,
													width: i.chartX - e.chartX,
													height: i.chartY - e.chartY,
												};
											};
											i.transform({
												axes: i.axes.filter(
													(t) =>
														t.zoomEnabled &&
														((this.zoomHor && t.horiz) ||
															(this.zoomVert && !t.horiz))
												),
												to: e(o),
												from: e(r),
												trigger: t.type,
											});
									  }),
									  e.res && ((e.res = !1), this.reset(!1, 0))),
								(e.lastTouches = o);
						}
						reset(t, e) {
							let i = this.chart,
								s = i.hoverSeries,
								r = i.hoverPoint,
								o = i.hoverPoints,
								a = i.tooltip,
								n = a && a.shared ? o : r;
							t &&
								n &&
								b(n).forEach(function (e) {
									e.series.isCartesian && void 0 === e.plotX && (t = !1);
								}),
								t
									? a &&
									  n &&
									  b(n).length &&
									  (a.refresh(n),
									  a.shared && o
											? o.forEach(function (t) {
													t.setState(t.state, !0),
														t.series.isCartesian &&
															(t.series.xAxis.crosshair &&
																t.series.xAxis.drawCrosshair(null, t),
															t.series.yAxis.crosshair &&
																t.series.yAxis.drawCrosshair(null, t));
											  })
											: r &&
											  (r.setState(r.state, !0),
											  i.axes.forEach(function (t) {
													t.crosshair &&
														r.series[t.coll] === t &&
														t.drawCrosshair(null, r);
											  })))
									: (r && r.onMouseOut(),
									  o &&
											o.forEach(function (t) {
												t.setState();
											}),
									  s && s.onMouseOut(),
									  a && a.hide(e),
									  this.unDocMouseMove &&
											(this.unDocMouseMove = this.unDocMouseMove()),
									  i.axes.forEach(function (t) {
											t.hideCrosshair();
									  }),
									  (i.hoverPoints = i.hoverPoint = void 0));
						}
						runPointActions(t, e, i) {
							let s = this.chart,
								r = s.series,
								a = s.tooltip && s.tooltip.options.enabled ? s.tooltip : void 0,
								h = !!a && a.shared,
								l = e || s.hoverPoint,
								d = (l && l.series) || s.hoverSeries,
								p =
									(!t || "touchmove" !== t.type) &&
									(!!e || (d && d.directTouch && this.isDirectTouch)),
								u = this.getHoverData(l, d, r, p, h, t);
							(l = u.hoverPoint), (d = u.hoverSeries);
							let g = u.hoverPoints,
								f =
									d &&
									d.tooltipOptions.followPointer &&
									!d.tooltipOptions.split,
								m = h && d && !d.noSharedTooltip;
							if (l && (i || l !== s.hoverPoint || (a && a.isHidden))) {
								if (
									((s.hoverPoints || []).forEach(function (t) {
										-1 === g.indexOf(t) && t.setState();
									}),
									s.hoverSeries !== d && d.onMouseOver(),
									this.applyInactiveState(g),
									(g || []).forEach(function (t) {
										t.setState("hover");
									}),
									s.hoverPoint && s.hoverPoint.firePointEvent("mouseOut"),
									!l.series)
								)
									return;
								(s.hoverPoints = g),
									(s.hoverPoint = l),
									l.firePointEvent("mouseOver", void 0, () => {
										a && l && a.refresh(m ? g : l, t);
									});
							} else if (f && a && !a.isHidden) {
								let e = a.getAnchor([{}], t);
								s.isInsidePlot(e[0], e[1], { visiblePlotOnly: !0 }) &&
									a.updatePosition({ plotX: e[0], plotY: e[1] });
							}
							this.unDocMouseMove ||
								((this.unDocMouseMove = n(
									s.container.ownerDocument,
									"mousemove",
									(t) =>
										o[v.hoverChartIndex ?? -1]?.pointer?.onDocumentMouseMove(t)
								)),
								this.eventsToUnbind.push(this.unDocMouseMove)),
								s.axes.forEach(function (e) {
									let i;
									let r = x((e.crosshair || {}).snap, !0);
									!r ||
										((i = s.hoverPoint) && i.series[e.coll] === e) ||
										(i = c(g, (t) => t.series && t.series[e.coll] === e)),
										i || !r ? e.drawCrosshair(t, i) : e.hideCrosshair();
								});
						}
						setDOMEvents() {
							let t = this.chart.container,
								e = t.ownerDocument;
							(t.onmousedown = this.onContainerMouseDown.bind(this)),
								(t.onmousemove = this.onContainerMouseMove.bind(this)),
								(t.onclick = this.onContainerClick.bind(this)),
								this.eventsToUnbind.push(
									n(t, "mouseenter", this.onContainerMouseEnter.bind(this)),
									n(t, "mouseleave", this.onContainerMouseLeave.bind(this))
								),
								v.unbindDocumentMouseUp ||
									(v.unbindDocumentMouseUp = n(
										e,
										"mouseup",
										this.onDocumentMouseUp.bind(this)
									));
							let i = this.chart.renderTo.parentElement;
							for (; i && "BODY" !== i.tagName; )
								this.eventsToUnbind.push(
									n(i, "scroll", () => {
										delete this.chartPosition;
									})
								),
									(i = i.parentElement);
							this.eventsToUnbind.push(
								n(t, "touchstart", this.onContainerTouchStart.bind(this), {
									passive: !1,
								}),
								n(t, "touchmove", this.onContainerTouchMove.bind(this), {
									passive: !1,
								})
							),
								v.unbindDocumentTouchEnd ||
									(v.unbindDocumentTouchEnd = n(
										e,
										"touchend",
										this.onDocumentTouchEnd.bind(this),
										{ passive: !1 }
									));
						}
						setHoverChartIndex(t) {
							let i = this.chart,
								s = e.charts[x(v.hoverChartIndex, -1)];
							s &&
								s !== i &&
								s.pointer?.onContainerMouseLeave(
									t || { relatedTarget: i.container }
								),
								(s && s.mouseIsDown) || (v.hoverChartIndex = i.index);
						}
						touch(t, e) {
							let i;
							let { chart: s, pinchDown: r = [] } = this;
							this.setHoverChartIndex(),
								1 === t.touches.length
									? ((t = this.normalize(t)),
									  s.isInsidePlot(
											t.chartX - s.plotLeft,
											t.chartY - s.plotTop,
											{ visiblePlotOnly: !0 }
									  ) && !s.openMenu
											? (e && this.runPointActions(t),
											  "touchmove" === t.type &&
													(i =
														!!r[0] &&
														Math.pow(r[0].chartX - t.chartX, 2) +
															Math.pow(r[0].chartY - t.chartY, 2) >=
															16),
											  x(i, !0) && this.pinch(t))
											: e && this.reset())
									: 2 === t.touches.length && this.pinch(t);
						}
						touchSelect(t) {
							return !!(
								this.chart.zooming.singleTouch &&
								t.touches &&
								1 === t.touches.length
							);
						}
						zoomOption(t) {
							let e = this.chart,
								i = e.inverted,
								s = e.zooming.type || "",
								r,
								o;
							/touch/.test(t.type) && (s = x(e.zooming.pinchType, s)),
								(this.zoomX = r = /x/.test(s)),
								(this.zoomY = o = /y/.test(s)),
								(this.zoomHor = (r && !i) || (o && i)),
								(this.zoomVert = (o && !i) || (r && i)),
								(this.hasZoom = r || o);
						}
					}
					return (
						((s = v || (v = {})).compose = function (t) {
							y(a, "Core.Pointer") &&
								n(t, "beforeRender", function () {
									this.pointer = new s(this, this.options);
								});
						}),
						v
					);
				}
			),
			i(
				e,
				"Core/Legend/Legend.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Templating.js"],
					e["Core/Globals.js"],
					e["Core/Series/Point.js"],
					e["Core/Renderer/RendererUtilities.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r, o) {
					var a;
					let { animObject: n, setAnimation: h } = t,
						{ format: l } = e,
						{ composed: d, marginNames: c } = i,
						{ distribute: p } = r,
						{
							addEvent: u,
							createElement: g,
							css: f,
							defined: m,
							discardElement: x,
							find: y,
							fireEvent: b,
							isNumber: v,
							merge: S,
							pick: k,
							pushUnique: C,
							relativeLength: M,
							stableSort: w,
							syncTimeout: T,
						} = o;
					class A {
						constructor(t, e) {
							(this.allItems = []),
								(this.initialItemY = 0),
								(this.itemHeight = 0),
								(this.itemMarginBottom = 0),
								(this.itemMarginTop = 0),
								(this.itemX = 0),
								(this.itemY = 0),
								(this.lastItemY = 0),
								(this.lastLineHeight = 0),
								(this.legendHeight = 0),
								(this.legendWidth = 0),
								(this.maxItemWidth = 0),
								(this.maxLegendWidth = 0),
								(this.offsetWidth = 0),
								(this.padding = 0),
								(this.pages = []),
								(this.symbolHeight = 0),
								(this.symbolWidth = 0),
								(this.titleHeight = 0),
								(this.totalItemWidth = 0),
								(this.widthOption = 0),
								(this.chart = t),
								this.setOptions(e),
								e.enabled &&
									(this.render(),
									u(this.chart, "endResize", function () {
										this.legend.positionCheckboxes();
									})),
								u(this.chart, "render", () => {
									this.options.enabled &&
										this.proximate &&
										(this.proximatePositions(), this.positionItems());
								});
						}
						setOptions(t) {
							let e = k(t.padding, 8);
							(this.options = t),
								this.chart.styledMode ||
									((this.itemStyle = t.itemStyle),
									(this.itemHiddenStyle = S(
										this.itemStyle,
										t.itemHiddenStyle
									))),
								(this.itemMarginTop = t.itemMarginTop),
								(this.itemMarginBottom = t.itemMarginBottom),
								(this.padding = e),
								(this.initialItemY = e - 5),
								(this.symbolWidth = k(t.symbolWidth, 16)),
								(this.pages = []),
								(this.proximate =
									"proximate" === t.layout && !this.chart.inverted),
								(this.baseline = void 0);
						}
						update(t, e) {
							let i = this.chart;
							this.setOptions(S(!0, this.options, t)),
								this.destroy(),
								(i.isDirtyLegend = i.isDirtyBox = !0),
								k(e, !0) && i.redraw(),
								b(this, "afterUpdate", { redraw: e });
						}
						colorizeItem(t, e) {
							let {
								area: i,
								group: s,
								label: r,
								line: o,
								symbol: a,
							} = t.legendItem || {};
							if (
								(s?.[e ? "removeClass" : "addClass"](
									"highcharts-legend-item-hidden"
								),
								!this.chart.styledMode)
							) {
								let { itemHiddenStyle: s = {} } = this,
									n = s.color,
									{
										fillColor: h,
										fillOpacity: l,
										lineColor: d,
										marker: c,
									} = t.options,
									p = (t) => (
										!e && (t.fill && (t.fill = n), t.stroke && (t.stroke = n)),
										t
									);
								r?.css(S(e ? this.itemStyle : s)),
									o?.attr(p({ stroke: d || t.color })),
									a &&
										a.attr(
											p(c && a.isMarker ? t.pointAttribs() : { fill: t.color })
										),
									i?.attr(
										p({ fill: h || t.color, "fill-opacity": h ? 1 : l ?? 0.75 })
									);
							}
							b(this, "afterColorizeItem", { item: t, visible: e });
						}
						positionItems() {
							this.allItems.forEach(this.positionItem, this),
								this.chart.isResizing || this.positionCheckboxes();
						}
						positionItem(t) {
							let { group: e, x: i = 0, y: s = 0 } = t.legendItem || {},
								r = this.options,
								o = r.symbolPadding,
								a = !r.rtl,
								n = t.checkbox;
							if (e && e.element) {
								let r = {
									translateX: a ? i : this.legendWidth - i - 2 * o - 4,
									translateY: s,
								};
								e[m(e.translateY) ? "animate" : "attr"](r, void 0, () => {
									b(this, "afterPositionItem", { item: t });
								});
							}
							n && ((n.x = i), (n.y = s));
						}
						destroyItem(t) {
							let e = t.checkbox,
								i = t.legendItem || {};
							for (let t of ["group", "label", "line", "symbol"])
								i[t] && (i[t] = i[t].destroy());
							e && x(e), (t.legendItem = void 0);
						}
						destroy() {
							for (let t of this.getAllItems()) this.destroyItem(t);
							for (let t of [
								"clipRect",
								"up",
								"down",
								"pager",
								"nav",
								"box",
								"title",
								"group",
							])
								this[t] && (this[t] = this[t].destroy());
							this.display = null;
						}
						positionCheckboxes() {
							let t;
							let e = this.group && this.group.alignAttr,
								i = this.clipHeight || this.legendHeight,
								s = this.titleHeight;
							e &&
								((t = e.translateY),
								this.allItems.forEach(function (r) {
									let o;
									let a = r.checkbox;
									a &&
										((o = t + s + a.y + (this.scrollOffset || 0) + 3),
										f(a, {
											left: e.translateX + r.checkboxOffset + a.x - 20 + "px",
											top: o + "px",
											display:
												this.proximate || (o > t - 6 && o < t + i - 6)
													? ""
													: "none",
										}));
								}, this));
						}
						renderTitle() {
							let t = this.options,
								e = this.padding,
								i = t.title,
								s,
								r = 0;
							i.text &&
								(this.title ||
									((this.title = this.chart.renderer
										.label(
											i.text,
											e - 3,
											e - 4,
											void 0,
											void 0,
											void 0,
											t.useHTML,
											void 0,
											"legend-title"
										)
										.attr({ zIndex: 1 })),
									this.chart.styledMode || this.title.css(i.style),
									this.title.add(this.group)),
								i.width ||
									this.title.css({ width: this.maxLegendWidth + "px" }),
								(r = (s = this.title.getBBox()).height),
								(this.offsetWidth = s.width),
								this.contentGroup.attr({ translateY: r })),
								(this.titleHeight = r);
						}
						setText(t) {
							let e = this.options;
							t.legendItem.label.attr({
								text: e.labelFormat
									? l(e.labelFormat, t, this.chart)
									: e.labelFormatter.call(t),
							});
						}
						renderItem(t) {
							let e = (t.legendItem = t.legendItem || {}),
								i = this.chart,
								s = i.renderer,
								r = this.options,
								o = "horizontal" === r.layout,
								a = this.symbolWidth,
								n = r.symbolPadding || 0,
								h = this.itemStyle,
								l = this.itemHiddenStyle,
								d = o ? k(r.itemDistance, 20) : 0,
								c = !r.rtl,
								p = !t.series,
								u = !p && t.series.drawLegendSymbol ? t.series : t,
								g = u.options,
								f = !!this.createCheckboxForItem && g && g.showCheckbox,
								m = r.useHTML,
								x = t.options.className,
								y = e.label,
								b = a + n + d + (f ? 20 : 0);
							!y &&
								((e.group = s
									.g("legend-item")
									.addClass(
										"highcharts-" +
											u.type +
											"-series highcharts-color-" +
											t.colorIndex +
											(x ? " " + x : "") +
											(p ? " highcharts-series-" + t.index : "")
									)
									.attr({ zIndex: 1 })
									.add(this.scrollGroup)),
								(e.label = y =
									s.text("", c ? a + n : -n, this.baseline || 0, m)),
								i.styledMode || y.css(S(t.visible ? h : l)),
								y.attr({ align: c ? "left" : "right", zIndex: 2 }).add(e.group),
								!this.baseline &&
									((this.fontMetrics = s.fontMetrics(y)),
									(this.baseline = this.fontMetrics.f + 3 + this.itemMarginTop),
									y.attr("y", this.baseline),
									(this.symbolHeight = k(r.symbolHeight, this.fontMetrics.f)),
									r.squareSymbol &&
										((this.symbolWidth = k(
											r.symbolWidth,
											Math.max(this.symbolHeight, 16)
										)),
										(b = this.symbolWidth + n + d + (f ? 20 : 0)),
										c && y.attr("x", this.symbolWidth + n))),
								u.drawLegendSymbol(this, t),
								this.setItemEvents && this.setItemEvents(t, y, m)),
								f &&
									!t.checkbox &&
									this.createCheckboxForItem &&
									this.createCheckboxForItem(t),
								this.colorizeItem(t, t.visible),
								(i.styledMode || !h.width) &&
									y.css({
										width:
											(r.itemWidth || this.widthOption || i.spacingBox.width) -
											b +
											"px",
									}),
								this.setText(t);
							let v = y.getBBox(),
								C = (this.fontMetrics && this.fontMetrics.h) || 0;
							(t.itemWidth = t.checkboxOffset =
								r.itemWidth || e.labelWidth || v.width + b),
								(this.maxItemWidth = Math.max(this.maxItemWidth, t.itemWidth)),
								(this.totalItemWidth += t.itemWidth),
								(this.itemHeight = t.itemHeight =
									Math.round(
										e.labelHeight || (v.height > 1.5 * C ? v.height : C)
									));
						}
						layoutItem(t) {
							let e = this.options,
								i = this.padding,
								s = "horizontal" === e.layout,
								r = t.itemHeight,
								o = this.itemMarginBottom,
								a = this.itemMarginTop,
								n = s ? k(e.itemDistance, 20) : 0,
								h = this.maxLegendWidth,
								l =
									e.alignColumns && this.totalItemWidth > h
										? this.maxItemWidth
										: t.itemWidth,
								d = t.legendItem || {};
							s &&
								this.itemX - i + l > h &&
								((this.itemX = i),
								this.lastLineHeight &&
									(this.itemY += a + this.lastLineHeight + o),
								(this.lastLineHeight = 0)),
								(this.lastItemY = a + this.itemY + o),
								(this.lastLineHeight = Math.max(r, this.lastLineHeight)),
								(d.x = this.itemX),
								(d.y = this.itemY),
								s
									? (this.itemX += l)
									: ((this.itemY += a + r + o), (this.lastLineHeight = r)),
								(this.offsetWidth =
									this.widthOption ||
									Math.max(
										(s ? this.itemX - i - (t.checkbox ? 0 : n) : l) + i,
										this.offsetWidth
									));
						}
						getAllItems() {
							let t = [];
							return (
								this.chart.series.forEach(function (e) {
									let i = e && e.options;
									e &&
										k(i.showInLegend, !m(i.linkedTo) && void 0, !0) &&
										(t = t.concat(
											(e.legendItem || {}).labels ||
												("point" === i.legendType ? e.data : e)
										));
								}),
								b(this, "afterGetAllItems", { allItems: t }),
								t
							);
						}
						getAlignment() {
							let t = this.options;
							return this.proximate
								? t.align.charAt(0) + "tv"
								: t.floating
								? ""
								: t.align.charAt(0) +
								  t.verticalAlign.charAt(0) +
								  t.layout.charAt(0);
						}
						adjustMargins(t, e) {
							let i = this.chart,
								s = this.options,
								r = this.getAlignment();
							r &&
								[
									/(lth|ct|rth)/,
									/(rtv|rm|rbv)/,
									/(rbh|cb|lbh)/,
									/(lbv|lm|ltv)/,
								].forEach(function (o, a) {
									o.test(r) &&
										!m(t[a]) &&
										(i[c[a]] = Math.max(
											i[c[a]],
											i.legend[(a + 1) % 2 ? "legendHeight" : "legendWidth"] +
												[1, -1, -1, 1][a] * s[a % 2 ? "x" : "y"] +
												k(s.margin, 12) +
												e[a] +
												(i.titleOffset[a] || 0)
										));
								});
						}
						proximatePositions() {
							let t;
							let e = this.chart,
								i = [],
								s = "left" === this.options.align;
							for (let r of (this.allItems.forEach(function (t) {
								let r,
									o,
									a = s,
									n,
									h;
								t.yAxis &&
									(t.xAxis.options.reversed && (a = !a),
									t.points &&
										(r = y(
											a ? t.points : t.points.slice(0).reverse(),
											function (t) {
												return v(t.plotY);
											}
										)),
									(o =
										this.itemMarginTop +
										t.legendItem.label.getBBox().height +
										this.itemMarginBottom),
									(h = t.yAxis.top - e.plotTop),
									(n = t.visible
										? (r ? r.plotY : t.yAxis.height) + (h - 0.3 * o)
										: h + t.yAxis.height),
									i.push({ target: n, size: o, item: t }));
							}, this),
							p(i, e.plotHeight)))
								(t = r.item.legendItem || {}),
									v(r.pos) && (t.y = e.plotTop - e.spacing[0] + r.pos);
						}
						render() {
							let t = this.chart,
								e = t.renderer,
								i = this.options,
								s = this.padding,
								r = this.getAllItems(),
								o,
								a,
								n,
								h = this.group,
								l,
								d = this.box;
							(this.itemX = s),
								(this.itemY = this.initialItemY),
								(this.offsetWidth = 0),
								(this.lastItemY = 0),
								(this.widthOption = M(i.width, t.spacingBox.width - s)),
								(l = t.spacingBox.width - 2 * s - i.x),
								["rm", "lm"].indexOf(this.getAlignment().substring(0, 2)) >
									-1 && (l /= 2),
								(this.maxLegendWidth = this.widthOption || l),
								h ||
									((this.group = h =
										e
											.g("legend")
											.addClass(i.className || "")
											.attr({ zIndex: 7 })
											.add()),
									(this.contentGroup = e.g().attr({ zIndex: 1 }).add(h)),
									(this.scrollGroup = e.g().add(this.contentGroup))),
								this.renderTitle(),
								w(
									r,
									(t, e) =>
										((t.options && t.options.legendIndex) || 0) -
										((e.options && e.options.legendIndex) || 0)
								),
								i.reversed && r.reverse(),
								(this.allItems = r),
								(this.display = o = !!r.length),
								(this.lastLineHeight = 0),
								(this.maxItemWidth = 0),
								(this.totalItemWidth = 0),
								(this.itemHeight = 0),
								r.forEach(this.renderItem, this),
								r.forEach(this.layoutItem, this),
								(a = (this.widthOption || this.offsetWidth) + s),
								(n = this.lastItemY + this.lastLineHeight + this.titleHeight),
								(n = this.handleOverflow(n) + s),
								d ||
									(this.box = d =
										e
											.rect()
											.addClass("highcharts-legend-box")
											.attr({ r: i.borderRadius })
											.add(h)),
								t.styledMode ||
									d
										.attr({
											stroke: i.borderColor,
											"stroke-width": i.borderWidth || 0,
											fill: i.backgroundColor || "none",
										})
										.shadow(i.shadow),
								a > 0 &&
									n > 0 &&
									d[d.placed ? "animate" : "attr"](
										d.crisp.call(
											{},
											{ x: 0, y: 0, width: a, height: n },
											d.strokeWidth()
										)
									),
								h[o ? "show" : "hide"](),
								t.styledMode && "none" === h.getStyle("display") && (a = n = 0),
								(this.legendWidth = a),
								(this.legendHeight = n),
								o && this.align(),
								this.proximate || this.positionItems(),
								b(this, "afterRender");
						}
						align(t = this.chart.spacingBox) {
							let e = this.chart,
								i = this.options,
								s = t.y;
							/(lth|ct|rth)/.test(this.getAlignment()) && e.titleOffset[0] > 0
								? (s += e.titleOffset[0])
								: /(lbh|cb|rbh)/.test(this.getAlignment()) &&
								  e.titleOffset[2] > 0 &&
								  (s -= e.titleOffset[2]),
								s !== t.y && (t = S(t, { y: s })),
								e.hasRendered || (this.group.placed = !1),
								this.group.align(
									S(i, {
										width: this.legendWidth,
										height: this.legendHeight,
										verticalAlign: this.proximate ? "top" : i.verticalAlign,
									}),
									!0,
									t
								);
						}
						handleOverflow(t) {
							let e = this,
								i = this.chart,
								s = i.renderer,
								r = this.options,
								o = r.y,
								a = "top" === r.verticalAlign,
								n = this.padding,
								h = r.maxHeight,
								l = r.navigation,
								d = k(l.animation, !0),
								c = l.arrowSize || 12,
								p = this.pages,
								u = this.allItems,
								g = function (t) {
									"number" == typeof t
										? S.attr({ height: t })
										: S && ((e.clipRect = S.destroy()), e.contentGroup.clip()),
										e.contentGroup.div &&
											(e.contentGroup.div.style.clip = t
												? "rect(" + n + "px,9999px," + (n + t) + "px,0)"
												: "auto");
								},
								f = function (t) {
									return (
										(e[t] = s
											.circle(0, 0, 1.3 * c)
											.translate(c / 2, c / 2)
											.add(v)),
										i.styledMode || e[t].attr("fill", "rgba(0,0,0,0.0001)"),
										e[t]
									);
								},
								m,
								x,
								y,
								b = i.spacingBox.height + (a ? -o : o) - n,
								v = this.nav,
								S = this.clipRect;
							return (
								"horizontal" !== r.layout ||
									"middle" === r.verticalAlign ||
									r.floating ||
									(b /= 2),
								h && (b = Math.min(b, h)),
								(p.length = 0),
								t && b > 0 && t > b && !1 !== l.enabled
									? ((this.clipHeight = m =
											Math.max(b - 20 - this.titleHeight - n, 0)),
									  (this.currentPage = k(this.currentPage, 1)),
									  (this.fullHeight = t),
									  u.forEach((t, e) => {
											let i = (y = t.legendItem || {}).y || 0,
												s = Math.round(y.label.getBBox().height),
												r = p.length;
											(!r || (i - p[r - 1] > m && (x || i) !== p[r - 1])) &&
												(p.push(x || i), r++),
												(y.pageIx = r - 1),
												x && ((u[e - 1].legendItem || {}).pageIx = r - 1),
												e === u.length - 1 &&
													i + s - p[r - 1] > m &&
													i > p[r - 1] &&
													(p.push(i), (y.pageIx = r)),
												i !== x && (x = i);
									  }),
									  S ||
											((S = e.clipRect = s.clipRect(0, n - 2, 9999, 0)),
											e.contentGroup.clip(S)),
									  g(m),
									  v ||
											((this.nav = v =
												s.g().attr({ zIndex: 1 }).add(this.group)),
											(this.up = s.symbol("triangle", 0, 0, c, c).add(v)),
											f("upTracker").on("click", function () {
												e.scroll(-1, d);
											}),
											(this.pager = s
												.text("", 15, 10)
												.addClass("highcharts-legend-navigation")),
											!i.styledMode && l.style && this.pager.css(l.style),
											this.pager.add(v),
											(this.down = s
												.symbol("triangle-down", 0, 0, c, c)
												.add(v)),
											f("downTracker").on("click", function () {
												e.scroll(1, d);
											})),
									  e.scroll(0),
									  (t = b))
									: v &&
									  (g(),
									  (this.nav = v.destroy()),
									  this.scrollGroup.attr({ translateY: 1 }),
									  (this.clipHeight = 0)),
								t
							);
						}
						scroll(t, e) {
							let i = this.chart,
								s = this.pages,
								r = s.length,
								o = this.clipHeight,
								a = this.options.navigation,
								l = this.pager,
								d = this.padding,
								c = this.currentPage + t;
							c > r && (c = r),
								c > 0 &&
									(void 0 !== e && h(e, i),
									this.nav.attr({
										translateX: d,
										translateY: o + this.padding + 7 + this.titleHeight,
										visibility: "inherit",
									}),
									[this.up, this.upTracker].forEach(function (t) {
										t.attr({
											class:
												1 === c
													? "highcharts-legend-nav-inactive"
													: "highcharts-legend-nav-active",
										});
									}),
									l.attr({ text: c + "/" + r }),
									[this.down, this.downTracker].forEach(function (t) {
										t.attr({
											x: 18 + this.pager.getBBox().width,
											class:
												c === r
													? "highcharts-legend-nav-inactive"
													: "highcharts-legend-nav-active",
										});
									}, this),
									i.styledMode ||
										(this.up.attr({
											fill: 1 === c ? a.inactiveColor : a.activeColor,
										}),
										this.upTracker.css({
											cursor: 1 === c ? "default" : "pointer",
										}),
										this.down.attr({
											fill: c === r ? a.inactiveColor : a.activeColor,
										}),
										this.downTracker.css({
											cursor: c === r ? "default" : "pointer",
										})),
									(this.scrollOffset = -s[c - 1] + this.initialItemY),
									this.scrollGroup.animate({ translateY: this.scrollOffset }),
									(this.currentPage = c),
									this.positionCheckboxes(),
									T(() => {
										b(this, "afterScroll", { currentPage: c });
									}, n(k(e, i.renderer.globalAnimation, !0)).duration));
						}
						setItemEvents(t, e, i) {
							let r = this,
								o = t.legendItem || {},
								a = r.chart.renderer.boxWrapper,
								n = t instanceof s,
								h = "highcharts-legend-" + (n ? "point" : "series") + "-active",
								l = r.chart.styledMode,
								d = i ? [e, o.symbol] : [o.group],
								c = (e) => {
									r.allItems.forEach((i) => {
										t !== i &&
											[i].concat(i.linkedSeries || []).forEach((t) => {
												t.setState(e, !n);
											});
									});
								};
							for (let i of d)
								i &&
									i
										.on("mouseover", function () {
											t.visible && c("inactive"),
												t.setState("hover"),
												t.visible && a.addClass(h),
												l || e.css(r.options.itemHoverStyle);
										})
										.on("mouseout", function () {
											r.chart.styledMode ||
												e.css(S(t.visible ? r.itemStyle : r.itemHiddenStyle)),
												c(""),
												a.removeClass(h),
												t.setState();
										})
										.on("click", function (e) {
											let i = "legendItemClick",
												s = function () {
													t.setVisible && t.setVisible(),
														c(t.visible ? "inactive" : "");
												};
											a.removeClass(h),
												(e = { browserEvent: e }),
												t.firePointEvent
													? t.firePointEvent(i, e, s)
													: b(t, i, e, s);
										});
						}
						createCheckboxForItem(t) {
							(t.checkbox = g(
								"input",
								{
									type: "checkbox",
									className: "highcharts-legend-checkbox",
									checked: t.selected,
									defaultChecked: t.selected,
								},
								this.options.itemCheckboxStyle,
								this.chart.container
							)),
								u(t.checkbox, "click", function (e) {
									let i = e.target;
									b(
										t.series || t,
										"checkboxClick",
										{ checked: i.checked, item: t },
										function () {
											t.select();
										}
									);
								});
						}
					}
					return (
						((a = A || (A = {})).compose = function (t) {
							C(d, "Core.Legend") &&
								u(t, "beforeMargins", function () {
									this.legend = new a(this, this.options.legend);
								});
						}),
						A
					);
				}
			),
			i(
				e,
				"Core/Legend/LegendSymbol.js",
				[e["Core/Utilities.js"]],
				function (t) {
					var e;
					let { extend: i, merge: s, pick: r } = t;
					return (
						(function (t) {
							function e(t, e, o) {
								let a = (this.legendItem = this.legendItem || {}),
									{ chart: n, options: h } = this,
									{ baseline: l = 0, symbolWidth: d, symbolHeight: c } = t,
									p = this.symbol || "circle",
									u = c / 2,
									g = n.renderer,
									f = a.group,
									m = l - Math.round(c * (o ? 0.4 : 0.3)),
									x = {},
									y,
									b = h.marker,
									v = 0;
								if (
									(n.styledMode ||
										((x["stroke-width"] = Math.min(h.lineWidth || 0, 24)),
										h.dashStyle
											? (x.dashstyle = h.dashStyle)
											: "square" === h.linecap ||
											  (x["stroke-linecap"] = "round")),
									(a.line = g
										.path()
										.addClass("highcharts-graph")
										.attr(x)
										.add(f)),
									o && (a.area = g.path().addClass("highcharts-area").add(f)),
									x["stroke-linecap"] &&
										(v = Math.min(a.line.strokeWidth(), d) / 2),
									d)
								) {
									let t = [
										["M", v, m],
										["L", d - v, m],
									];
									a.line.attr({ d: t }),
										a.area?.attr({ d: [...t, ["L", d - v, l], ["L", v, l]] });
								}
								if (b && !1 !== b.enabled && d) {
									let t = Math.min(r(b.radius, u), u);
									0 === p.indexOf("url") &&
										((b = s(b, { width: c, height: c })), (t = 0)),
										(a.symbol = y =
											g
												.symbol(
													p,
													d / 2 - t,
													m - t,
													2 * t,
													2 * t,
													i({ context: "legend" }, b)
												)
												.addClass("highcharts-point")
												.add(f)),
										(y.isMarker = !0);
								}
							}
							(t.areaMarker = function (t, i) {
								e.call(this, t, i, !0);
							}),
								(t.lineMarker = e),
								(t.rectangle = function (t, e) {
									let i = e.legendItem || {},
										s = t.options,
										o = t.symbolHeight,
										a = s.squareSymbol,
										n = a ? o : t.symbolWidth;
									i.symbol = this.chart.renderer
										.rect(
											a ? (t.symbolWidth - o) / 2 : 0,
											t.baseline - o + 1,
											n,
											o,
											r(t.options.symbolRadius, o / 2)
										)
										.addClass("highcharts-point")
										.attr({ zIndex: 3 })
										.add(i.group);
								});
						})(e || (e = {})),
						e
					);
				}
			),
			i(e, "Core/Series/SeriesDefaults.js", [], function () {
				return {
					lineWidth: 2,
					allowPointSelect: !1,
					crisp: !0,
					showCheckbox: !1,
					animation: { duration: 1e3 },
					enableMouseTracking: !0,
					events: {},
					marker: {
						enabledThreshold: 2,
						lineColor: "#ffffff",
						lineWidth: 0,
						radius: 4,
						states: {
							normal: { animation: !0 },
							hover: {
								animation: { duration: 150 },
								enabled: !0,
								radiusPlus: 2,
								lineWidthPlus: 1,
							},
							select: {
								fillColor: "#cccccc",
								lineColor: "#000000",
								lineWidth: 2,
							},
						},
					},
					point: { events: {} },
					dataLabels: {
						animation: {},
						align: "center",
						borderWidth: 0,
						defer: !0,
						formatter: function () {
							let { numberFormatter: t } = this.series.chart;
							return "number" != typeof this.y ? "" : t(this.y, -1);
						},
						padding: 5,
						style: {
							fontSize: "0.7em",
							fontWeight: "bold",
							color: "contrast",
							textOutline: "1px contrast",
						},
						verticalAlign: "bottom",
						x: 0,
						y: 0,
					},
					cropThreshold: 300,
					opacity: 1,
					pointRange: 0,
					softThreshold: !0,
					states: {
						normal: { animation: !0 },
						hover: {
							animation: { duration: 150 },
							lineWidthPlus: 1,
							marker: {},
							halo: { size: 10, opacity: 0.25 },
						},
						select: { animation: { duration: 0 } },
						inactive: { animation: { duration: 150 }, opacity: 0.2 },
					},
					stickyTracking: !0,
					turboThreshold: 1e3,
					findNearestPointBy: "x",
				};
			}),
			i(
				e,
				"Core/Series/SeriesRegistry.js",
				[
					e["Core/Globals.js"],
					e["Core/Defaults.js"],
					e["Core/Series/Point.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s) {
					var r;
					let { defaultOptions: o } = e,
						{ extend: a, extendClass: n, merge: h } = s;
					return (
						(function (e) {
							function s(t, s) {
								let r = o.plotOptions || {},
									a = s.defaultOptions,
									n = s.prototype;
								return (
									(n.type = t),
									n.pointClass || (n.pointClass = i),
									!e.seriesTypes[t] &&
										(a && (r[t] = a), (e.seriesTypes[t] = s), !0)
								);
							}
							(e.seriesTypes = t.seriesTypes),
								(e.registerSeriesType = s),
								(e.seriesType = function (t, r, l, d, c) {
									let p = o.plotOptions || {};
									if (
										((r = r || ""),
										(p[t] = h(p[r], l)),
										delete e.seriesTypes[t],
										s(t, n(e.seriesTypes[r] || function () {}, d)),
										(e.seriesTypes[t].prototype.type = t),
										c)
									) {
										class s extends i {}
										a(s.prototype, c),
											(e.seriesTypes[t].prototype.pointClass = s);
									}
									return e.seriesTypes[t];
								});
						})(r || (r = {})),
						r
					);
				}
			),
			i(
				e,
				"Core/Series/Series.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Defaults.js"],
					e["Core/Foundation.js"],
					e["Core/Globals.js"],
					e["Core/Legend/LegendSymbol.js"],
					e["Core/Series/Point.js"],
					e["Core/Series/SeriesDefaults.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Renderer/SVG/SVGElement.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r, o, a, n, h, l) {
					let { animObject: d, setAnimation: c } = t,
						{ defaultOptions: p } = e,
						{ registerEventOptions: u } = i,
						{ svg: g, win: f } = s,
						{ seriesTypes: m } = n,
						{
							arrayMax: x,
							arrayMin: y,
							clamp: b,
							correctFloat: v,
							defined: S,
							destroyObjectProperties: k,
							diffObjects: C,
							erase: M,
							error: w,
							extend: T,
							find: A,
							fireEvent: P,
							getClosestDistance: L,
							getNestedProperty: O,
							insertItem: D,
							isArray: E,
							isNumber: I,
							isString: j,
							merge: B,
							objectEach: R,
							pick: z,
							removeEvent: N,
							splat: W,
							syncTimeout: G,
						} = l;
					class H {
						constructor() {
							this.zoneAxis = "y";
						}
						init(t, e) {
							let i;
							P(this, "init", { options: e });
							let s = this,
								r = t.series;
							(this.eventsToUnbind = []),
								(s.chart = t),
								(s.options = s.setOptions(e));
							let o = s.options,
								a = !1 !== o.visible;
							(s.linkedSeries = []),
								s.bindAxes(),
								T(s, {
									name: o.name,
									state: "",
									visible: a,
									selected: !0 === o.selected,
								}),
								u(this, o);
							let n = o.events;
							((n && n.click) ||
								(o.point && o.point.events && o.point.events.click) ||
								o.allowPointSelect) &&
								(t.runTrackerClick = !0),
								s.getColor(),
								s.getSymbol(),
								s.parallelArrays.forEach(function (t) {
									s[t + "Data"] || (s[t + "Data"] = []);
								}),
								s.isCartesian && (t.hasCartesianSeries = !0),
								r.length && (i = r[r.length - 1]),
								(s._i = z(i && i._i, -1) + 1),
								(s.opacity = s.options.opacity),
								t.orderItems("series", D(this, r)),
								o.dataSorting && o.dataSorting.enabled
									? s.setDataSortingOptions()
									: s.points || s.data || s.setData(o.data, !1),
								P(this, "afterInit");
						}
						is(t) {
							return m[t] && this instanceof m[t];
						}
						bindAxes() {
							let t;
							let e = this,
								i = e.options,
								s = e.chart;
							P(this, "bindAxes", null, function () {
								(e.axisTypes || []).forEach(function (r) {
									s[r].forEach(function (s) {
										(t = s.options),
											(z(i[r], 0) === s.index ||
												(void 0 !== i[r] && i[r] === t.id)) &&
												(D(e, s.series), (e[r] = s), (s.isDirty = !0));
									}),
										e[r] || e.optionalAxis === r || w(18, !0, s);
								});
							}),
								P(this, "afterBindAxes");
						}
						updateParallelArrays(t, e, i) {
							let s = t.series,
								r = I(e)
									? function (i) {
											let r = "y" === i && s.toYData ? s.toYData(t) : t[i];
											s[i + "Data"][e] = r;
									  }
									: function (t) {
											Array.prototype[e].apply(s[t + "Data"], i);
									  };
							s.parallelArrays.forEach(r);
						}
						hasData() {
							return (
								(this.visible &&
									void 0 !== this.dataMax &&
									void 0 !== this.dataMin) ||
								(this.visible && this.yData && this.yData.length > 0)
							);
						}
						hasMarkerChanged(t, e) {
							let i = t.marker,
								s = e.marker || {};
							return (
								i &&
								((s.enabled && !i.enabled) ||
									s.symbol !== i.symbol ||
									s.height !== i.height ||
									s.width !== i.width)
							);
						}
						autoIncrement(t) {
							let e = this.options,
								i = e.pointIntervalUnit,
								s = e.relativeXValue,
								r = this.chart.time,
								o = this.xIncrement,
								a,
								n;
							return ((o = z(o, e.pointStart, 0)),
							(this.pointInterval = n =
								z(this.pointInterval, e.pointInterval, 1)),
							s && I(t) && (n *= t),
							i &&
								((a = new r.Date(o)),
								"day" === i
									? r.set("Date", a, r.get("Date", a) + n)
									: "month" === i
									? r.set("Month", a, r.get("Month", a) + n)
									: "year" === i &&
									  r.set("FullYear", a, r.get("FullYear", a) + n),
								(n = a.getTime() - o)),
							s && I(t))
								? o + n
								: ((this.xIncrement = o + n), o);
						}
						setDataSortingOptions() {
							let t = this.options;
							T(this, {
								requireSorting: !1,
								sorted: !1,
								enabledDataSorting: !0,
								allowDG: !1,
							}),
								S(t.pointRange) || (t.pointRange = 1);
						}
						setOptions(t) {
							let e;
							let i = this.chart,
								s = i.options.plotOptions,
								r = i.userOptions || {},
								o = B(t),
								a = i.styledMode,
								n = { plotOptions: s, userOptions: o };
							P(this, "setOptions", n);
							let h = n.plotOptions[this.type],
								l = r.plotOptions || {},
								d = l.series || {},
								c = p.plotOptions[this.type] || {},
								u = l[this.type] || {};
							this.userOptions = n.userOptions;
							let g = B(h, s.series, u, o);
							(this.tooltipOptions = B(
								p.tooltip,
								p.plotOptions.series?.tooltip,
								c?.tooltip,
								i.userOptions.tooltip,
								l.series?.tooltip,
								u.tooltip,
								o.tooltip
							)),
								(this.stickyTracking = z(
									o.stickyTracking,
									u.stickyTracking,
									d.stickyTracking,
									(!!this.tooltipOptions.shared && !this.noSharedTooltip) ||
										g.stickyTracking
								)),
								null === h.marker && delete g.marker,
								(this.zoneAxis = g.zoneAxis || "y");
							let f = (this.zones = (g.zones || []).map((t) => ({ ...t })));
							return (
								(g.negativeColor || g.negativeFillColor) &&
									!g.zones &&
									((e = {
										value: g[this.zoneAxis + "Threshold"] || g.threshold || 0,
										className: "highcharts-negative",
									}),
									a ||
										((e.color = g.negativeColor),
										(e.fillColor = g.negativeFillColor)),
									f.push(e)),
								f.length &&
									S(f[f.length - 1].value) &&
									f.push(
										a ? {} : { color: this.color, fillColor: this.fillColor }
									),
								P(this, "afterSetOptions", { options: g }),
								g
							);
						}
						getName() {
							return z(this.options.name, "Series " + (this.index + 1));
						}
						getCyclic(t, e, i) {
							let s, r;
							let o = this.chart,
								a = `${t}Index`,
								n = `${t}Counter`,
								h = i?.length || o.options.chart.colorCount;
							!e &&
								(S(
									(r = z(
										"color" === t ? this.options.colorIndex : void 0,
										this[a]
									))
								)
									? (s = r)
									: (o.series.length || (o[n] = 0),
									  (s = o[n] % h),
									  (o[n] += 1)),
								i && (e = i[s])),
								void 0 !== s && (this[a] = s),
								(this[t] = e);
						}
						getColor() {
							this.chart.styledMode
								? this.getCyclic("color")
								: this.options.colorByPoint
								? (this.color = "#cccccc")
								: this.getCyclic(
										"color",
										this.options.color || p.plotOptions[this.type].color,
										this.chart.options.colors
								  );
						}
						getPointsCollection() {
							return (this.hasGroupedData ? this.points : this.data) || [];
						}
						getSymbol() {
							let t = this.options.marker;
							this.getCyclic("symbol", t.symbol, this.chart.options.symbols);
						}
						findPointIndex(t, e) {
							let i, s, r;
							let a = t.id,
								n = t.x,
								h = this.points,
								l = this.options.dataSorting;
							if (a) {
								let t = this.chart.get(a);
								t instanceof o && (i = t);
							} else if (
								this.linkedParent ||
								this.enabledDataSorting ||
								this.options.relativeXValue
							) {
								let e = (e) => !e.touched && e.index === t.index;
								if (
									(l && l.matchByName
										? (e = (e) => !e.touched && e.name === t.name)
										: this.options.relativeXValue &&
										  (e = (e) => !e.touched && e.options.x === t.x),
									!(i = A(h, e)))
								)
									return;
							}
							return (
								i && void 0 !== (r = i && i.index) && (s = !0),
								void 0 === r && I(n) && (r = this.xData.indexOf(n, e)),
								-1 !== r &&
									void 0 !== r &&
									this.cropped &&
									(r = r >= this.cropStart ? r - this.cropStart : r),
								!s && I(r) && h[r] && h[r].touched && (r = void 0),
								r
							);
						}
						updateData(t, e) {
							let i = this.options,
								s = i.dataSorting,
								r = this.points,
								o = [],
								a = this.requireSorting,
								n = t.length === r.length,
								h,
								l,
								d,
								c,
								p = !0;
							if (
								((this.xIncrement = null),
								t.forEach(function (t, e) {
									let l;
									let d =
											(S(t) &&
												this.pointClass.prototype.optionsToObject.call(
													{ series: this },
													t
												)) ||
											{},
										p = d.x;
									d.id || I(p)
										? (-1 === (l = this.findPointIndex(d, c)) || void 0 === l
												? o.push(t)
												: r[l] && t !== i.data[l]
												? (r[l].update(t, !1, null, !1),
												  (r[l].touched = !0),
												  a && (c = l + 1))
												: r[l] && (r[l].touched = !0),
										  (!n ||
												e !== l ||
												(s && s.enabled) ||
												this.hasDerivedData) &&
												(h = !0))
										: o.push(t);
								}, this),
								h)
							)
								for (l = r.length; l--; )
									(d = r[l]) && !d.touched && d.remove && d.remove(!1, e);
							else
								!n || (s && s.enabled)
									? (p = !1)
									: (t.forEach(function (t, e) {
											t === r[e].y ||
												r[e].destroyed ||
												r[e].update(t, !1, null, !1);
									  }),
									  (o.length = 0));
							return (
								r.forEach(function (t) {
									t && (t.touched = !1);
								}),
								!!p &&
									(o.forEach(function (t) {
										this.addPoint(t, !1, null, null, !1);
									}, this),
									null === this.xIncrement &&
										this.xData &&
										this.xData.length &&
										((this.xIncrement = x(this.xData)), this.autoIncrement()),
									!0)
							);
						}
						setData(t, e = !0, i, s) {
							let r = this,
								o = r.points,
								a = (o && o.length) || 0,
								n = r.options,
								h = r.chart,
								l = n.dataSorting,
								d = r.xAxis,
								c = n.turboThreshold,
								p = this.xData,
								u = this.yData,
								g = r.pointArrayMap,
								f = g && g.length,
								m = n.keys,
								x,
								y,
								b,
								v = 0,
								S = 1,
								k = null,
								C;
							h.options.chart.allowMutatingData ||
								(n.data && delete r.options.data,
								r.userOptions.data && delete r.userOptions.data,
								(C = B(!0, t)));
							let M = (t = C || t || []).length;
							if (
								(l && l.enabled && (t = this.sortData(t)),
								h.options.chart.allowMutatingData &&
									!1 !== s &&
									M &&
									a &&
									!r.cropped &&
									!r.hasGroupedData &&
									r.visible &&
									!r.boosted &&
									(b = this.updateData(t, i)),
								!b)
							) {
								if (
									((r.xIncrement = null),
									(r.colorCounter = 0),
									this.parallelArrays.forEach(function (t) {
										r[t + "Data"].length = 0;
									}),
									c && M > c)
								) {
									if (I((k = r.getFirstValidPoint(t))))
										for (x = 0; x < M; x++)
											(p[x] = this.autoIncrement()), (u[x] = t[x]);
									else if (E(k)) {
										if (f) {
											if (k.length === f)
												for (x = 0; x < M; x++)
													(p[x] = this.autoIncrement()), (u[x] = t[x]);
											else
												for (x = 0; x < M; x++)
													(y = t[x]), (p[x] = y[0]), (u[x] = y.slice(1, f + 1));
										} else if (
											(m &&
												((v = m.indexOf("x")),
												(S = m.indexOf("y")),
												(v = v >= 0 ? v : 0),
												(S = S >= 0 ? S : 1)),
											1 === k.length && (S = 0),
											v === S)
										)
											for (x = 0; x < M; x++)
												(p[x] = this.autoIncrement()), (u[x] = t[x][S]);
										else
											for (x = 0; x < M; x++)
												(y = t[x]), (p[x] = y[v]), (u[x] = y[S]);
									} else w(12, !1, h);
								} else
									for (x = 0; x < M; x++)
										(y = { series: r }),
											r.pointClass.prototype.applyOptions.apply(y, [t[x]]),
											r.updateParallelArrays(y, x);
								for (
									u && j(u[0]) && w(14, !0, h),
										r.data = [],
										r.options.data = r.userOptions.data = t,
										x = a;
									x--;

								)
									o[x]?.destroy();
								d && (d.minRange = d.userMinRange),
									(r.isDirty = h.isDirtyBox = !0),
									(r.isDirtyData = !!o),
									(i = !1);
							}
							"point" === n.legendType &&
								(this.processData(), this.generatePoints()),
								e && h.redraw(i);
						}
						sortData(t) {
							let e = this,
								i = e.options.dataSorting.sortKey || "y",
								s = function (t, e) {
									return (
										(S(e) &&
											t.pointClass.prototype.optionsToObject.call(
												{ series: t },
												e
											)) ||
										{}
									);
								};
							return (
								t.forEach(function (i, r) {
									(t[r] = s(e, i)), (t[r].index = r);
								}, this),
								t
									.concat()
									.sort((t, e) => {
										let s = O(i, t),
											r = O(i, e);
										return r < s ? -1 : r > s ? 1 : 0;
									})
									.forEach(function (t, e) {
										t.x = e;
									}, this),
								e.linkedSeries &&
									e.linkedSeries.forEach(function (e) {
										let i = e.options,
											r = i.data;
										(i.dataSorting && i.dataSorting.enabled) ||
											!r ||
											(r.forEach(function (i, o) {
												(r[o] = s(e, i)),
													t[o] && ((r[o].x = t[o].x), (r[o].index = o));
											}),
											e.setData(r, !1));
									}),
								t
							);
						}
						getProcessedData(t) {
							let e = this,
								i = e.xAxis,
								s = e.options,
								r = s.cropThreshold,
								o = t || e.getExtremesFromAll || s.getExtremesFromAll,
								a = i?.logarithmic,
								n = e.isCartesian,
								h,
								l,
								d = 0,
								c,
								p,
								u,
								g = e.xData,
								f = e.yData,
								m = !1,
								x = g.length;
							i &&
								((p = (c = i.getExtremes()).min),
								(u = c.max),
								(m = !!(i.categories && !i.names.length))),
								n &&
									e.sorted &&
									!o &&
									(!r || x > r || e.forceCrop) &&
									(g[x - 1] < p || g[0] > u
										? ((g = []), (f = []))
										: e.yData &&
										  (g[0] < p || g[x - 1] > u) &&
										  ((g = (h = this.cropData(e.xData, e.yData, p, u)).xData),
										  (f = h.yData),
										  (d = h.start),
										  (l = !0)));
							let y = L(
								[a ? g.map(a.log2lin) : g],
								() => e.requireSorting && !m && w(15, !1, e.chart)
							);
							return {
								xData: g,
								yData: f,
								cropped: l,
								cropStart: d,
								closestPointRange: y,
							};
						}
						processData(t) {
							let e = this.xAxis;
							if (
								this.isCartesian &&
								!this.isDirty &&
								!e.isDirty &&
								!this.yAxis.isDirty &&
								!t
							)
								return !1;
							let i = this.getProcessedData();
							(this.cropped = i.cropped),
								(this.cropStart = i.cropStart),
								(this.processedXData = i.xData),
								(this.processedYData = i.yData),
								(this.closestPointRange = this.basePointRange =
									i.closestPointRange),
								P(this, "afterProcessData");
						}
						cropData(t, e, i, s) {
							let r = t.length,
								o,
								a,
								n = 0,
								h = r;
							for (o = 0; o < r; o++)
								if (t[o] >= i) {
									n = Math.max(0, o - 1);
									break;
								}
							for (a = o; a < r; a++)
								if (t[a] > s) {
									h = a + 1;
									break;
								}
							return {
								xData: t.slice(n, h),
								yData: e.slice(n, h),
								start: n,
								end: h,
							};
						}
						generatePoints() {
							let t = this.options,
								e = this.processedData || t.data,
								i = this.processedXData,
								s = this.processedYData,
								r = this.pointClass,
								o = i.length,
								a = this.cropStart || 0,
								n = this.hasGroupedData,
								h = t.keys,
								l = [],
								d = t.dataGrouping && t.dataGrouping.groupAll ? a : 0,
								c,
								p,
								u,
								g,
								f = this.data;
							if (!f && !n) {
								let t = [];
								(t.length = e.length), (f = this.data = t);
							}
							for (h && n && (this.options.keys = !1), g = 0; g < o; g++)
								(p = a + g),
									n
										? (((u = new r(this, [i[g]].concat(W(s[g])))).dataGroup =
												this.groupMap[d + g]),
										  u.dataGroup.options &&
												((u.options = u.dataGroup.options),
												T(u, u.dataGroup.options),
												delete u.dataLabels))
										: (u = f[p]) ||
										  void 0 === e[p] ||
										  (f[p] = u = new r(this, e[p], i[g])),
									u && ((u.index = n ? d + g : p), (l[g] = u));
							if (((this.options.keys = h), f && (o !== (c = f.length) || n)))
								for (g = 0; g < c; g++)
									g !== a || n || (g += o),
										f[g] && (f[g].destroyElements(), (f[g].plotX = void 0));
							(this.data = f),
								(this.points = l),
								P(this, "afterGeneratePoints");
						}
						getXExtremes(t) {
							return { min: y(t), max: x(t) };
						}
						getExtremes(t, e) {
							let i = this.xAxis,
								s = this.yAxis,
								r = this.processedXData || this.xData,
								o = [],
								a = this.requireSorting && !this.is("column") ? 1 : 0,
								n = !!s && s.positiveValuesOnly,
								h,
								l,
								d,
								c,
								p,
								u,
								g,
								f = 0,
								m = 0,
								b = 0,
								v = (t = t || this.stackedYData || this.processedYData || [])
									.length;
							for (
								i && ((f = (h = i.getExtremes()).min), (m = h.max)), u = 0;
								u < v;
								u++
							)
								if (
									((c = r[u]),
									(l =
										(I((p = t[u])) || E(p)) &&
										((I(p) ? p > 0 : p.length) || !n)),
									(d =
										e ||
										this.getExtremesFromAll ||
										this.options.getExtremesFromAll ||
										this.cropped ||
										!i ||
										((r[u + a] || c) >= f && (r[u - a] || c) <= m)),
									l && d)
								) {
									if ((g = p.length)) for (; g--; ) I(p[g]) && (o[b++] = p[g]);
									else o[b++] = p;
								}
							let S = { activeYData: o, dataMin: y(o), dataMax: x(o) };
							return P(this, "afterGetExtremes", { dataExtremes: S }), S;
						}
						applyExtremes() {
							let t = this.getExtremes();
							return (this.dataMin = t.dataMin), (this.dataMax = t.dataMax), t;
						}
						getFirstValidPoint(t) {
							let e = t.length,
								i = 0,
								s = null;
							for (; null === s && i < e; ) (s = t[i]), i++;
							return s;
						}
						translate() {
							this.processedXData || this.processData(), this.generatePoints();
							let t = this.options,
								e = t.stacking,
								i = this.xAxis,
								s = i.categories,
								r = this.enabledDataSorting,
								o = this.yAxis,
								a = this.points,
								n = a.length,
								h = this.pointPlacementToXValue(),
								l = !!h,
								d = t.threshold,
								c = t.startFromThreshold ? d : 0,
								p,
								u,
								g,
								f,
								m = Number.MAX_VALUE;
							function x(t) {
								return b(t, -1e5, 1e5);
							}
							for (p = 0; p < n; p++) {
								let t;
								let n = a[p],
									y = n.x,
									b,
									k,
									C = n.y,
									M = n.low,
									w =
										e &&
										o.stacking?.stacks[
											(this.negStacks && C < (c ? 0 : d) ? "-" : "") +
												this.stackKey
										];
								(u = i.translate(y, !1, !1, !1, !0, h)),
									(n.plotX = I(u) ? v(x(u)) : void 0),
									e &&
										this.visible &&
										w &&
										w[y] &&
										((f = this.getStackIndicator(f, y, this.index)),
										!n.isNull && f.key && (k = (b = w[y]).points[f.key]),
										b &&
											E(k) &&
											((M = k[0]),
											(C = k[1]),
											M === c &&
												f.key === w[y].base &&
												(M = z(I(d) ? d : o.min)),
											o.positiveValuesOnly && S(M) && M <= 0 && (M = void 0),
											(n.total = n.stackTotal = z(b.total)),
											(n.percentage =
												S(n.y) && b.total ? (n.y / b.total) * 100 : void 0),
											(n.stackY = C),
											this.irregularWidths ||
												b.setOffset(
													this.pointXOffset || 0,
													this.barW || 0,
													void 0,
													void 0,
													void 0,
													this.xAxis
												))),
									(n.yBottom = S(M)
										? x(o.translate(M, !1, !0, !1, !0))
										: void 0),
									this.dataModify && (C = this.dataModify.modifyValue(C, p)),
									I(C) &&
										void 0 !== n.plotX &&
										(t = I((t = o.translate(C, !1, !0, !1, !0)))
											? x(t)
											: void 0),
									(n.plotY = t),
									(n.isInside = this.isPointInside(n)),
									(n.clientX = l ? v(i.translate(y, !1, !1, !1, !0, h)) : u),
									(n.negative = (n.y || 0) < (d || 0)),
									(n.category = z(s && s[n.x], n.x)),
									n.isNull ||
										!1 === n.visible ||
										(void 0 !== g && (m = Math.min(m, Math.abs(u - g))),
										(g = u)),
									(n.zone = this.zones.length ? n.getZone() : void 0),
									!n.graphic && this.group && r && (n.isNew = !0);
							}
							(this.closestPointRangePx = m), P(this, "afterTranslate");
						}
						getValidPoints(t, e, i) {
							let s = this.chart;
							return (t || this.points || []).filter(function (t) {
								let { plotX: r, plotY: o } = t;
								return (
									!!(
										(i || (!t.isNull && I(o))) &&
										(!e || s.isInsidePlot(r, o, { inverted: s.inverted }))
									) && !1 !== t.visible
								);
							});
						}
						getClipBox() {
							let { chart: t, xAxis: e, yAxis: i } = this,
								{ x: s, y: r, width: o, height: a } = B(t.clipBox);
							return (
								e && e.len !== t.plotSizeX && (o = e.len),
								i && i.len !== t.plotSizeY && (a = i.len),
								t.inverted && !this.invertible && ([o, a] = [a, o]),
								{ x: s, y: r, width: o, height: a }
							);
						}
						getSharedClipKey() {
							return (
								(this.sharedClipKey =
									(this.options.xAxis || 0) + "," + (this.options.yAxis || 0)),
								this.sharedClipKey
							);
						}
						setClip() {
							let { chart: t, group: e, markerGroup: i } = this,
								s = t.sharedClips,
								r = t.renderer,
								o = this.getClipBox(),
								a = this.getSharedClipKey(),
								n = s[a];
							n ? n.animate(o) : (s[a] = n = r.clipRect(o)),
								e && e.clip(!1 === this.options.clip ? void 0 : n),
								i && i.clip();
						}
						animate(t) {
							let { chart: e, group: i, markerGroup: s } = this,
								r = e.inverted,
								o = d(this.options.animation),
								a = [
									this.getSharedClipKey(),
									o.duration,
									o.easing,
									o.defer,
								].join(","),
								n = e.sharedClips[a],
								h = e.sharedClips[a + "m"];
							if (t && i) {
								let t = this.getClipBox();
								if (n) n.attr("height", t.height);
								else {
									(t.width = 0),
										r && (t.x = e.plotHeight),
										(n = e.renderer.clipRect(t)),
										(e.sharedClips[a] = n);
									let i = {
										x: -99,
										y: -99,
										width: r ? e.plotWidth + 199 : 99,
										height: r ? 99 : e.plotHeight + 199,
									};
									(h = e.renderer.clipRect(i)), (e.sharedClips[a + "m"] = h);
								}
								i.clip(n), s?.clip(h);
							} else if (n && !n.hasClass("highcharts-animating")) {
								let t = this.getClipBox(),
									i = o.step;
								(s?.element.childNodes.length || e.series.length > 1) &&
									(o.step = function (t, e) {
										i && i.apply(e, arguments),
											"width" === e.prop &&
												h?.element &&
												h.attr(r ? "height" : "width", t + 99);
									}),
									n.addClass("highcharts-animating").animate(t, o);
							}
						}
						afterAnimate() {
							this.setClip(),
								R(this.chart.sharedClips, (t, e, i) => {
									t &&
										!this.chart.container.querySelector(
											`[clip-path="url(#${t.id})"]`
										) &&
										(t.destroy(), delete i[e]);
								}),
								(this.finishedAnimating = !0),
								P(this, "afterAnimate");
						}
						drawPoints(t = this.points) {
							let e, i, s, r, o, a, n;
							let h = this.chart,
								l = h.styledMode,
								{ colorAxis: d, options: c } = this,
								p = c.marker,
								u = this[this.specialGroup || "markerGroup"],
								g = this.xAxis,
								f = z(
									p.enabled,
									!g || !!g.isRadial || null,
									this.closestPointRangePx >= p.enabledThreshold * p.radius
								);
							if (!1 !== p.enabled || this._hasPointMarkers)
								for (e = 0; e < t.length; e++)
									if (
										((r = (s = (i = t[e]).graphic) ? "animate" : "attr"),
										(o = i.marker || {}),
										(a = !!i.marker),
										((f && void 0 === o.enabled) || o.enabled) &&
											!i.isNull &&
											!1 !== i.visible)
									) {
										let t = z(o.symbol, this.symbol, "rect");
										(n = this.markerAttribs(i, i.selected && "select")),
											this.enabledDataSorting &&
												(i.startXPos = g.reversed ? -(n.width || 0) : g.width);
										let e = !1 !== i.isInside;
										if (
											(!s &&
												e &&
												((n.width || 0) > 0 || i.hasImage) &&
												((i.graphic = s =
													h.renderer
														.symbol(t, n.x, n.y, n.width, n.height, a ? o : p)
														.add(u)),
												this.enabledDataSorting &&
													h.hasRendered &&
													(s.attr({ x: i.startXPos }), (r = "animate"))),
											s &&
												"animate" === r &&
												s[e ? "show" : "hide"](e).animate(n),
											s)
										) {
											let t = this.pointAttribs(
												i,
												l || !i.selected ? void 0 : "select"
											);
											l ? d && s.css({ fill: t.fill }) : s[r](t);
										}
										s && s.addClass(i.getClassName(), !0);
									} else s && (i.graphic = s.destroy());
						}
						markerAttribs(t, e) {
							let i = this.options,
								s = i.marker,
								r = t.marker || {},
								o = r.symbol || s.symbol,
								a = {},
								n,
								h,
								l = z(r.radius, s && s.radius);
							e &&
								((n = s.states[e]),
								(l = z(
									(h = r.states && r.states[e]) && h.radius,
									n && n.radius,
									l && l + ((n && n.radiusPlus) || 0)
								))),
								(t.hasImage = o && 0 === o.indexOf("url")),
								t.hasImage && (l = 0);
							let d = t.pos();
							return (
								I(l) &&
									d &&
									((a.x = d[0] - l),
									(a.y = d[1] - l),
									i.crisp && (a.x = Math.floor(a.x))),
								l && (a.width = a.height = 2 * l),
								a
							);
						}
						pointAttribs(t, e) {
							let i = this.options.marker,
								s = t && t.options,
								r = (s && s.marker) || {},
								o = s && s.color,
								a = t && t.color,
								n = t && t.zone && t.zone.color,
								h,
								l,
								d = this.color,
								c,
								p,
								u = z(r.lineWidth, i.lineWidth),
								g = 1;
							return (
								(d = o || n || a || d),
								(c = r.fillColor || i.fillColor || d),
								(p = r.lineColor || i.lineColor || d),
								(e = e || "normal"),
								(h = i.states[e] || {}),
								(u = z(
									(l = (r.states && r.states[e]) || {}).lineWidth,
									h.lineWidth,
									u + z(l.lineWidthPlus, h.lineWidthPlus, 0)
								)),
								(c = l.fillColor || h.fillColor || c),
								{
									stroke: (p = l.lineColor || h.lineColor || p),
									"stroke-width": u,
									fill: c,
									opacity: (g = z(l.opacity, h.opacity, g)),
								}
							);
						}
						destroy(t) {
							let e, i, s;
							let r = this,
								o = r.chart,
								a = /AppleWebKit\/533/.test(f.navigator.userAgent),
								n = r.data || [];
							for (
								P(r, "destroy", { keepEventsForUpdate: t }),
									this.removeEvents(t),
									(r.axisTypes || []).forEach(function (t) {
										(s = r[t]) &&
											s.series &&
											(M(s.series, r), (s.isDirty = s.forceRedraw = !0));
									}),
									r.legendItem && r.chart.legend.destroyItem(r),
									e = n.length;
								e--;

							)
								(i = n[e]) && i.destroy && i.destroy();
							for (let t of r.zones) k(t, void 0, !0);
							l.clearTimeout(r.animationTimeout),
								R(r, function (t, e) {
									t instanceof h &&
										!t.survive &&
										t[a && "group" === e ? "hide" : "destroy"]();
								}),
								o.hoverSeries === r && (o.hoverSeries = void 0),
								M(o.series, r),
								o.orderItems("series"),
								R(r, function (e, i) {
									(t && "hcEvents" === i) || delete r[i];
								});
						}
						applyZones() {
							let {
									area: t,
									chart: e,
									graph: i,
									zones: s,
									points: r,
									xAxis: o,
									yAxis: a,
									zoneAxis: n,
								} = this,
								{ inverted: h, renderer: l } = e,
								d = this[`${n}Axis`],
								{ isXAxis: c, len: p = 0 } = d || {},
								u = (i?.strokeWidth() || 0) / 2 + 1,
								g = (t, e = 0, i = 0) => {
									h && (i = p - i);
									let { translated: s = 0, lineClip: r } = t,
										o = i - s;
									r?.push([
										"L",
										e,
										Math.abs(o) < u ? i - u * (o <= 0 ? -1 : 1) : s,
									]);
								};
							if (s.length && (i || t) && d && I(d.min)) {
								let e = d.getExtremes().max,
									u = (t) => {
										t.forEach((e, i) => {
											("M" === e[0] || "L" === e[0]) &&
												(t[i] = [
													e[0],
													c ? p - e[1] : e[1],
													c ? e[2] : p - e[2],
												]);
										});
									};
								if (
									(s.forEach((t) => {
										(t.lineClip = []),
											(t.translated = b(
												d.toPixels(z(t.value, e), !0) || 0,
												0,
												p
											));
									}),
									i && !this.showLine && i.hide(),
									t && t.hide(),
									"y" === n && r.length < o.len)
								)
									for (let t of r) {
										let { plotX: e, plotY: i, zone: r } = t,
											o = r && s[s.indexOf(r) - 1];
										r && g(r, e, i), o && g(o, e, i);
									}
								let f = [],
									m = d.toPixels(d.getExtremes().min, !0);
								s.forEach((e) => {
									let s = e.lineClip || [],
										r = Math.round(e.translated || 0);
									o.reversed && s.reverse();
									let { clip: n, simpleClip: d } = e,
										p = 0,
										g = 0,
										x = o.len,
										y = a.len;
									c ? ((p = r), (x = m)) : ((g = r), (y = m));
									let b = [
											["M", p, g],
											["L", x, g],
											["L", x, y],
											["L", p, y],
											["Z"],
										],
										v = [b[0], ...s, b[1], b[2], ...f, b[3], b[4]];
									(f = s.reverse()),
										(m = r),
										h && (u(v), t && u(b)),
										n
											? (n.animate({ d: v }), d?.animate({ d: b }))
											: ((n = e.clip = l.path(v)),
											  t && (d = e.simpleClip = l.path(b))),
										i && e.graph?.clip(n),
										t && e.area?.clip(d);
								});
							} else this.visible && (i && i.show(), t && t.show());
						}
						plotGroup(t, e, i, s, r) {
							let o = this[t],
								a = !o,
								n = { visibility: i, zIndex: s || 0.1 };
							return (
								S(this.opacity) &&
									!this.chart.styledMode &&
									"inactive" !== this.state &&
									(n.opacity = this.opacity),
								o || (this[t] = o = this.chart.renderer.g().add(r)),
								o.addClass(
									"highcharts-" +
										e +
										" highcharts-series-" +
										this.index +
										" highcharts-" +
										this.type +
										"-series " +
										(S(this.colorIndex)
											? "highcharts-color-" + this.colorIndex + " "
											: "") +
										(this.options.className || "") +
										(o.hasClass("highcharts-tracker")
											? " highcharts-tracker"
											: ""),
									!0
								),
								o.attr(n)[a ? "attr" : "animate"](this.getPlotBox(e)),
								o
							);
						}
						getPlotBox(t) {
							let e = this.xAxis,
								i = this.yAxis,
								s = this.chart,
								r =
									s.inverted &&
									!s.polar &&
									e &&
									this.invertible &&
									"series" === t;
							return (
								s.inverted && ((e = i), (i = this.xAxis)),
								{
									translateX: e ? e.left : s.plotLeft,
									translateY: i ? i.top : s.plotTop,
									rotation: r ? 90 : 0,
									rotationOriginX: r ? (e.len - i.len) / 2 : 0,
									rotationOriginY: r ? (e.len + i.len) / 2 : 0,
									scaleX: r ? -1 : 1,
									scaleY: 1,
								}
							);
						}
						removeEvents(t) {
							let { eventsToUnbind: e } = this;
							t || N(this),
								e.length &&
									(e.forEach((t) => {
										t();
									}),
									(e.length = 0));
						}
						render() {
							let t = this,
								{ chart: e, options: i, hasRendered: s } = t,
								r = d(i.animation),
								o = t.visible ? "inherit" : "hidden",
								a = i.zIndex,
								n = e.seriesGroup,
								h = t.finishedAnimating ? 0 : r.duration;
							P(this, "render"),
								t.plotGroup("group", "series", o, a, n),
								(t.markerGroup = t.plotGroup(
									"markerGroup",
									"markers",
									o,
									a,
									n
								)),
								!1 !== i.clip && t.setClip(),
								h && t.animate?.(!0),
								t.drawGraph && (t.drawGraph(), t.applyZones()),
								t.visible && t.drawPoints(),
								t.drawDataLabels?.(),
								t.redrawPoints?.(),
								i.enableMouseTracking && t.drawTracker?.(),
								h && t.animate?.(),
								s ||
									(h && r.defer && (h += r.defer),
									(t.animationTimeout = G(() => {
										t.afterAnimate();
									}, h || 0))),
								(t.isDirty = !1),
								(t.hasRendered = !0),
								P(t, "afterRender");
						}
						redraw() {
							let t = this.isDirty || this.isDirtyData;
							this.translate(), this.render(), t && delete this.kdTree;
						}
						reserveSpace() {
							return (
								this.visible || !this.chart.options.chart.ignoreHiddenSeries
							);
						}
						searchPoint(t, e) {
							let { xAxis: i, yAxis: s } = this,
								r = this.chart.inverted;
							return this.searchKDTree(
								{
									clientX: r ? i.len - t.chartY + i.pos : t.chartX - i.pos,
									plotY: r ? s.len - t.chartX + s.pos : t.chartY - s.pos,
								},
								e,
								t
							);
						}
						buildKDTree(t) {
							this.buildingKdTree = !0;
							let e = this,
								i = e.options.findNearestPointBy.indexOf("y") > -1 ? 2 : 1;
							delete e.kdTree,
								G(
									function () {
										(e.kdTree = (function t(i, s, r) {
											let o, a;
											let n = i?.length;
											if (n)
												return (
													(o = e.kdAxisArray[s % r]),
													i.sort((t, e) => (t[o] || 0) - (e[o] || 0)),
													{
														point: i[(a = Math.floor(n / 2))],
														left: t(i.slice(0, a), s + 1, r),
														right: t(i.slice(a + 1), s + 1, r),
													}
												);
										})(e.getValidPoints(void 0, !e.directTouch), i, i)),
											(e.buildingKdTree = !1);
									},
									e.options.kdNow || t?.type === "touchstart" ? 0 : 1
								);
						}
						searchKDTree(t, e, i) {
							let s = this,
								[r, o] = this.kdAxisArray,
								a = e ? "distX" : "dist",
								n =
									(s.options.findNearestPointBy || "").indexOf("y") > -1
										? 2
										: 1,
								h = !!s.isBubble;
							if (
								(this.kdTree || this.buildingKdTree || this.buildKDTree(i),
								this.kdTree)
							)
								return (function t(e, i, n, l) {
									let d = i.point,
										c = s.kdAxisArray[n % l],
										p,
										u,
										g = d;
									!(function (t, e) {
										let i = t[r],
											s = e[r],
											a = S(i) && S(s) ? i - s : null,
											n = t[o],
											l = e[o],
											d = S(n) && S(l) ? n - l : 0,
											c = (h && e.marker?.radius) || 0;
										(e.dist = Math.sqrt(((a && a * a) || 0) + d * d) - c),
											(e.distX = S(a) ? Math.abs(a) - c : Number.MAX_VALUE);
									})(e, d);
									let f =
											(e[c] || 0) -
											(d[c] || 0) +
											((h && d.marker?.radius) || 0),
										m = f < 0 ? "left" : "right",
										x = f < 0 ? "right" : "left";
									return (
										i[m] && (g = (p = t(e, i[m], n + 1, l))[a] < g[a] ? p : d),
										i[x] &&
											Math.sqrt(f * f) < g[a] &&
											(g = (u = t(e, i[x], n + 1, l))[a] < g[a] ? u : g),
										g
									);
								})(t, this.kdTree, n, n);
						}
						pointPlacementToXValue() {
							let { options: t, xAxis: e } = this,
								i = t.pointPlacement;
							return (
								"between" === i && (i = e.reversed ? -0.5 : 0.5),
								I(i) ? i * (t.pointRange || e.pointRange) : 0
							);
						}
						isPointInside(t) {
							let { chart: e, xAxis: i, yAxis: s } = this,
								{ plotX: r = -1, plotY: o = -1 } = t;
							return (
								o >= 0 &&
								o <= (s ? s.len : e.plotHeight) &&
								r >= 0 &&
								r <= (i ? i.len : e.plotWidth)
							);
						}
						drawTracker() {
							let t = this,
								e = t.options,
								i = e.trackByArea,
								s = [].concat((i ? t.areaPath : t.graphPath) || []),
								r = t.chart,
								o = r.pointer,
								a = r.renderer,
								n = r.options.tooltip?.snap || 0,
								h = () => {
									e.enableMouseTracking &&
										r.hoverSeries !== t &&
										t.onMouseOver();
								},
								l = "rgba(192,192,192," + (g ? 1e-4 : 0.002) + ")",
								d = t.tracker;
							d
								? d.attr({ d: s })
								: t.graph &&
								  ((t.tracker = d =
										a
											.path(s)
											.attr({
												visibility: t.visible ? "inherit" : "hidden",
												zIndex: 2,
											})
											.addClass(
												i
													? "highcharts-tracker-area"
													: "highcharts-tracker-line"
											)
											.add(t.group)),
								  r.styledMode ||
										d.attr({
											"stroke-linecap": "round",
											"stroke-linejoin": "round",
											stroke: l,
											fill: i ? l : "none",
											"stroke-width": t.graph.strokeWidth() + (i ? 0 : 2 * n),
										}),
								  [t.tracker, t.markerGroup, t.dataLabelsGroup].forEach((t) => {
										t &&
											(t
												.addClass("highcharts-tracker")
												.on("mouseover", h)
												.on("mouseout", (t) => {
													o?.onTrackerMouseOut(t);
												}),
											e.cursor && !r.styledMode && t.css({ cursor: e.cursor }),
											t.on("touchstart", h));
								  })),
								P(this, "afterDrawTracker");
						}
						addPoint(t, e, i, s, r) {
							let o, a;
							let n = this.options,
								h = this.data,
								l = this.chart,
								d = this.xAxis,
								c = d && d.hasNames && d.names,
								p = n.data,
								u = this.xData;
							e = z(e, !0);
							let g = { series: this };
							this.pointClass.prototype.applyOptions.apply(g, [t]);
							let f = g.x;
							if (((a = u.length), this.requireSorting && f < u[a - 1]))
								for (o = !0; a && u[a - 1] > f; ) a--;
							this.updateParallelArrays(g, "splice", [a, 0, 0]),
								this.updateParallelArrays(g, a),
								c && g.name && (c[f] = g.name),
								p.splice(a, 0, t),
								(o || this.processedData) &&
									(this.data.splice(a, 0, null), this.processData()),
								"point" === n.legendType && this.generatePoints(),
								i &&
									(h[0] && h[0].remove
										? h[0].remove(!1)
										: (h.shift(),
										  this.updateParallelArrays(g, "shift"),
										  p.shift())),
								!1 !== r && P(this, "addPoint", { point: g }),
								(this.isDirty = !0),
								(this.isDirtyData = !0),
								e && l.redraw(s);
						}
						removePoint(t, e, i) {
							let s = this,
								r = s.data,
								o = r[t],
								a = s.points,
								n = s.chart,
								h = function () {
									a && a.length === r.length && a.splice(t, 1),
										r.splice(t, 1),
										s.options.data.splice(t, 1),
										s.updateParallelArrays(o || { series: s }, "splice", [
											t,
											1,
										]),
										o && o.destroy(),
										(s.isDirty = !0),
										(s.isDirtyData = !0),
										e && n.redraw();
								};
							c(i, n),
								(e = z(e, !0)),
								o ? o.firePointEvent("remove", null, h) : h();
						}
						remove(t, e, i, s) {
							let r = this,
								o = r.chart;
							function a() {
								r.destroy(s),
									(o.isDirtyLegend = o.isDirtyBox = !0),
									o.linkSeries(s),
									z(t, !0) && o.redraw(e);
							}
							!1 !== i ? P(r, "remove", null, a) : a();
						}
						update(t, e) {
							P(this, "update", { options: (t = C(t, this.userOptions)) });
							let i = this,
								s = i.chart,
								r = i.userOptions,
								o = i.initialType || i.type,
								a = s.options.plotOptions,
								n = m[o].prototype,
								h = i.finishedAnimating && { animation: !1 },
								l = {},
								d,
								c,
								p = [
									"colorIndex",
									"eventOptions",
									"navigatorSeries",
									"symbolIndex",
									"baseSeries",
								],
								u = t.type || r.type || s.options.chart.type,
								g = !(
									this.hasDerivedData ||
									(u && u !== this.type) ||
									void 0 !== t.pointStart ||
									void 0 !== t.pointInterval ||
									void 0 !== t.relativeXValue ||
									t.joinBy ||
									t.mapData ||
									[
										"dataGrouping",
										"pointStart",
										"pointInterval",
										"pointIntervalUnit",
										"keys",
									].some((t) => i.hasOptionChanged(t))
								);
							(u = u || o),
								g &&
									(p.push(
										"data",
										"isDirtyData",
										"isDirtyCanvas",
										"points",
										"processedData",
										"processedXData",
										"processedYData",
										"xIncrement",
										"cropped",
										"_hasPointMarkers",
										"hasDataLabels",
										"nodes",
										"layout",
										"level",
										"mapMap",
										"mapData",
										"minY",
										"maxY",
										"minX",
										"maxX",
										"transformGroups"
									),
									!1 !== t.visible && p.push("area", "graph"),
									i.parallelArrays.forEach(function (t) {
										p.push(t + "Data");
									}),
									t.data &&
										(t.dataSorting && T(i.options.dataSorting, t.dataSorting),
										this.setData(t.data, !1))),
								(t = B(
									r,
									{
										index: void 0 === r.index ? i.index : r.index,
										pointStart:
											a?.series?.pointStart ?? r.pointStart ?? i.xData?.[0],
									},
									!g && { data: i.options.data },
									t,
									h
								)),
								g && t.data && (t.data = i.options.data),
								(p = [
									"group",
									"markerGroup",
									"dataLabelsGroup",
									"transformGroup",
								].concat(p)).forEach(function (t) {
									(p[t] = i[t]), delete i[t];
								});
							let f = !1;
							if (m[u]) {
								if (((f = u !== i.type), i.remove(!1, !1, !1, !0), f)) {
									if ((s.propFromSeries(), Object.setPrototypeOf))
										Object.setPrototypeOf(i, m[u].prototype);
									else {
										let t =
											Object.hasOwnProperty.call(i, "hcEvents") && i.hcEvents;
										for (c in n) i[c] = void 0;
										T(i, m[u].prototype),
											t ? (i.hcEvents = t) : delete i.hcEvents;
									}
								}
							} else w(17, !0, s, { missingModuleFor: u });
							if (
								(p.forEach(function (t) {
									i[t] = p[t];
								}),
								i.init(s, t),
								g && this.points)
							)
								for (let t of (!1 === (d = i.options).visible
									? ((l.graphic = 1), (l.dataLabel = 1))
									: (this.hasMarkerChanged(d, r) && (l.graphic = 1),
									  i.hasDataLabels?.() || (l.dataLabel = 1)),
								this.points))
									t &&
										t.series &&
										(t.resolveColor(),
										Object.keys(l).length && t.destroyElements(l),
										!1 === d.showInLegend &&
											t.legendItem &&
											s.legend.destroyItem(t));
							(i.initialType = o),
								s.linkSeries(),
								s.setSortedData(),
								f && i.linkedSeries.length && (i.isDirtyData = !0),
								P(this, "afterUpdate"),
								z(e, !0) && s.redraw(!!g && void 0);
						}
						setName(t) {
							(this.name = this.options.name = this.userOptions.name = t),
								(this.chart.isDirtyLegend = !0);
						}
						hasOptionChanged(t) {
							let e = this.chart,
								i = this.options[t],
								s = e.options.plotOptions,
								r = this.userOptions[t],
								o = z(s?.[this.type]?.[t], s?.series?.[t]);
							return r && !S(o) ? i !== r : i !== z(o, i);
						}
						onMouseOver() {
							let t = this.chart,
								e = t.hoverSeries,
								i = t.pointer;
							i?.setHoverChartIndex(),
								e && e !== this && e.onMouseOut(),
								this.options.events.mouseOver && P(this, "mouseOver"),
								this.setState("hover"),
								(t.hoverSeries = this);
						}
						onMouseOut() {
							let t = this.options,
								e = this.chart,
								i = e.tooltip,
								s = e.hoverPoint;
							(e.hoverSeries = null),
								s && s.onMouseOut(),
								this && t.events.mouseOut && P(this, "mouseOut"),
								i &&
									!this.stickyTracking &&
									(!i.shared || this.noSharedTooltip) &&
									i.hide(),
								e.series.forEach(function (t) {
									t.setState("", !0);
								});
						}
						setState(t, e) {
							let i = this,
								s = i.options,
								r = i.graph,
								o = s.inactiveOtherPoints,
								a = s.states,
								n = z(
									a[t || "normal"] && a[t || "normal"].animation,
									i.chart.options.chart.animation
								),
								h = s.lineWidth,
								l = s.opacity;
							if (
								((t = t || ""),
								i.state !== t &&
									([i.group, i.markerGroup, i.dataLabelsGroup].forEach(
										function (e) {
											e &&
												(i.state &&
													e.removeClass("highcharts-series-" + i.state),
												t && e.addClass("highcharts-series-" + t));
										}
									),
									(i.state = t),
									!i.chart.styledMode))
							) {
								if (a[t] && !1 === a[t].enabled) return;
								if (
									(t &&
										((h = a[t].lineWidth || h + (a[t].lineWidthPlus || 0)),
										(l = z(a[t].opacity, l))),
									r && !r.dashstyle && I(h))
								)
									for (let t of [r, ...this.zones.map((t) => t.graph)])
										t?.animate({ "stroke-width": h }, n);
								o ||
									[
										i.group,
										i.markerGroup,
										i.dataLabelsGroup,
										i.labelBySeries,
									].forEach(function (t) {
										t && t.animate({ opacity: l }, n);
									});
							}
							e && o && i.points && i.setAllPointsToState(t || void 0);
						}
						setAllPointsToState(t) {
							this.points.forEach(function (e) {
								e.setState && e.setState(t);
							});
						}
						setVisible(t, e) {
							let i = this,
								s = i.chart,
								r = s.options.chart.ignoreHiddenSeries,
								o = i.visible;
							i.visible =
								t =
								i.options.visible =
								i.userOptions.visible =
									void 0 === t ? !o : t;
							let a = t ? "show" : "hide";
							[
								"group",
								"dataLabelsGroup",
								"markerGroup",
								"tracker",
								"tt",
							].forEach((t) => {
								i[t]?.[a]();
							}),
								(s.hoverSeries === i || s.hoverPoint?.series === i) &&
									i.onMouseOut(),
								i.legendItem && s.legend.colorizeItem(i, t),
								(i.isDirty = !0),
								i.options.stacking &&
									s.series.forEach((t) => {
										t.options.stacking && t.visible && (t.isDirty = !0);
									}),
								i.linkedSeries.forEach((e) => {
									e.setVisible(t, !1);
								}),
								r && (s.isDirtyBox = !0),
								P(i, a),
								!1 !== e && s.redraw();
						}
						show() {
							this.setVisible(!0);
						}
						hide() {
							this.setVisible(!1);
						}
						select(t) {
							(this.selected =
								t =
								this.options.selected =
									void 0 === t ? !this.selected : t),
								this.checkbox && (this.checkbox.checked = t),
								P(this, t ? "select" : "unselect");
						}
						shouldShowTooltip(t, e, i = {}) {
							return (
								(i.series = this),
								(i.visiblePlotOnly = !0),
								this.chart.isInsidePlot(t, e, i)
							);
						}
						drawLegendSymbol(t, e) {
							r[this.options.legendSymbol || "rectangle"]?.call(this, t, e);
						}
					}
					return (
						(H.defaultOptions = a),
						(H.types = n.seriesTypes),
						(H.registerType = n.registerSeriesType),
						T(H.prototype, {
							axisTypes: ["xAxis", "yAxis"],
							coll: "series",
							colorCounter: 0,
							directTouch: !1,
							invertible: !0,
							isCartesian: !0,
							kdAxisArray: ["clientX", "plotY"],
							parallelArrays: ["x", "y"],
							pointClass: o,
							requireSorting: !0,
							sorted: !0,
						}),
						(n.series = H),
						H
					);
				}
			),
			i(
				e,
				"Core/Chart/Chart.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Axis/Axis.js"],
					e["Core/Defaults.js"],
					e["Core/Templating.js"],
					e["Core/Foundation.js"],
					e["Core/Globals.js"],
					e["Core/Renderer/RendererRegistry.js"],
					e["Core/Series/Series.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Renderer/SVG/SVGRenderer.js"],
					e["Core/Time.js"],
					e["Core/Utilities.js"],
					e["Core/Renderer/HTML/AST.js"],
					e["Core/Axis/Tick.js"],
				],
				function (t, e, i, s, r, o, a, n, h, l, d, c, p, u) {
					let { animate: g, animObject: f, setAnimation: m } = t,
						{ defaultOptions: x, defaultTime: y } = i,
						{ numberFormat: b } = s,
						{ registerEventOptions: v } = r,
						{ charts: S, doc: k, marginNames: C, svg: M, win: w } = o,
						{ seriesTypes: T } = h,
						{
							addEvent: A,
							attr: P,
							createElement: L,
							css: O,
							defined: D,
							diffObjects: E,
							discardElement: I,
							erase: j,
							error: B,
							extend: R,
							find: z,
							fireEvent: N,
							getStyle: W,
							isArray: G,
							isNumber: H,
							isObject: X,
							isString: F,
							merge: Y,
							objectEach: U,
							pick: V,
							pInt: $,
							relativeLength: Z,
							removeEvent: _,
							splat: q,
							syncTimeout: K,
							uniqueKey: J,
						} = c;
					class Q {
						static chart(t, e, i) {
							return new Q(t, e, i);
						}
						constructor(t, e, i) {
							this.sharedClips = {};
							let s = [...arguments];
							(F(t) || t.nodeName) && (this.renderTo = s.shift()),
								this.init(s[0], s[1]);
						}
						setZoomOptions() {
							let t = this.options.chart,
								e = t.zooming;
							this.zooming = {
								...e,
								type: V(t.zoomType, e.type),
								key: V(t.zoomKey, e.key),
								pinchType: V(t.pinchType, e.pinchType),
								singleTouch: V(t.zoomBySingleTouch, e.singleTouch, !1),
								resetButton: Y(e.resetButton, t.resetZoomButton),
							};
						}
						init(t, e) {
							N(this, "init", { args: arguments }, function () {
								let i = Y(x, t),
									s = i.chart;
								(this.userOptions = R({}, t)),
									(this.margin = []),
									(this.spacing = []),
									(this.labelCollectors = []),
									(this.callback = e),
									(this.isResizing = 0),
									(this.options = i),
									(this.axes = []),
									(this.series = []),
									(this.time =
										t.time && Object.keys(t.time).length
											? new d(t.time)
											: o.time),
									(this.numberFormatter = s.numberFormatter || b),
									(this.styledMode = s.styledMode),
									(this.hasCartesianSeries = s.showAxes),
									(this.index = S.length),
									S.push(this),
									o.chartCount++,
									v(this, s),
									(this.xAxis = []),
									(this.yAxis = []),
									(this.pointCount =
										this.colorCounter =
										this.symbolCounter =
											0),
									this.setZoomOptions(),
									N(this, "afterInit"),
									this.firstRender();
							});
						}
						initSeries(t) {
							let e = this.options.chart,
								i = t.type || e.type,
								s = T[i];
							s || B(17, !0, this, { missingModuleFor: i });
							let r = new s();
							return "function" == typeof r.init && r.init(this, t), r;
						}
						setSortedData() {
							this.getSeriesOrderByLinks().forEach(function (t) {
								t.points ||
									t.data ||
									!t.enabledDataSorting ||
									t.setData(t.options.data, !1);
							});
						}
						getSeriesOrderByLinks() {
							return this.series.concat().sort(function (t, e) {
								return t.linkedSeries.length || e.linkedSeries.length
									? e.linkedSeries.length - t.linkedSeries.length
									: 0;
							});
						}
						orderItems(t, e = 0) {
							let i = this[t],
								s = (this.options[t] = q(this.options[t]).slice()),
								r = (this.userOptions[t] = this.userOptions[t]
									? q(this.userOptions[t]).slice()
									: []);
							if ((this.hasRendered && (s.splice(e), r.splice(e)), i))
								for (let t = e, o = i.length; t < o; ++t) {
									let e = i[t];
									e &&
										((e.index = t),
										e instanceof n && (e.name = e.getName()),
										e.options.isInternal ||
											((s[t] = e.options), (r[t] = e.userOptions)));
								}
						}
						isInsidePlot(t, e, i = {}) {
							let {
									inverted: s,
									plotBox: r,
									plotLeft: o,
									plotTop: a,
									scrollablePlotBox: n,
								} = this,
								{ scrollLeft: h = 0, scrollTop: l = 0 } =
									(i.visiblePlotOnly &&
										this.scrollablePlotArea?.scrollingContainer) ||
									{},
								d = i.series,
								c = (i.visiblePlotOnly && n) || r,
								p = i.inverted ? e : t,
								u = i.inverted ? t : e,
								g = { x: p, y: u, isInsidePlot: !0, options: i };
							if (!i.ignoreX) {
								let t = (d && (s && !this.polar ? d.yAxis : d.xAxis)) || {
										pos: o,
										len: 1 / 0,
									},
									e = i.paneCoordinates ? t.pos + p : o + p;
								(e >= Math.max(h + o, t.pos) &&
									e <= Math.min(h + o + c.width, t.pos + t.len)) ||
									(g.isInsidePlot = !1);
							}
							if (!i.ignoreY && g.isInsidePlot) {
								let t = (!s && i.axis && !i.axis.isXAxis && i.axis) ||
										(d && (s ? d.xAxis : d.yAxis)) || { pos: a, len: 1 / 0 },
									e = i.paneCoordinates ? t.pos + u : a + u;
								(e >= Math.max(l + a, t.pos) &&
									e <= Math.min(l + a + c.height, t.pos + t.len)) ||
									(g.isInsidePlot = !1);
							}
							return N(this, "afterIsInsidePlot", g), g.isInsidePlot;
						}
						redraw(t) {
							N(this, "beforeRedraw");
							let e = this.hasCartesianSeries
									? this.axes
									: this.colorAxis || [],
								i = this.series,
								s = this.pointer,
								r = this.legend,
								o = this.userOptions.legend,
								a = this.renderer,
								n = a.isHidden(),
								h = [],
								l,
								d,
								c,
								p = this.isDirtyBox,
								u = this.isDirtyLegend,
								g;
							for (
								a.rootFontSize = a.boxWrapper.getStyle("font-size"),
									this.setResponsive && this.setResponsive(!1),
									m(!!this.hasRendered && t, this),
									n && this.temporaryDisplay(),
									this.layOutTitles(!1),
									c = i.length;
								c--;

							)
								if (
									((g = i[c]).options.stacking || g.options.centerInCategory) &&
									((d = !0), g.isDirty)
								) {
									l = !0;
									break;
								}
							if (l)
								for (c = i.length; c--; )
									(g = i[c]).options.stacking && (g.isDirty = !0);
							i.forEach(function (t) {
								t.isDirty &&
									("point" === t.options.legendType
										? ("function" == typeof t.updateTotals && t.updateTotals(),
										  (u = !0))
										: o && (o.labelFormatter || o.labelFormat) && (u = !0)),
									t.isDirtyData && N(t, "updatedData");
							}),
								u &&
									r &&
									r.options.enabled &&
									(r.render(), (this.isDirtyLegend = !1)),
								d && this.getStacks(),
								e.forEach(function (t) {
									t.updateNames(), t.setScale();
								}),
								this.getMargins(),
								e.forEach(function (t) {
									t.isDirty && (p = !0);
								}),
								e.forEach(function (t) {
									let e = t.min + "," + t.max;
									t.extKey !== e &&
										((t.extKey = e),
										h.push(function () {
											N(t, "afterSetExtremes", R(t.eventArgs, t.getExtremes())),
												delete t.eventArgs;
										})),
										(p || d) && t.redraw();
								}),
								p && this.drawChartBox(),
								N(this, "predraw"),
								i.forEach(function (t) {
									(p || t.isDirty) && t.visible && t.redraw(),
										(t.isDirtyData = !1);
								}),
								s && s.reset(!0),
								a.draw(),
								N(this, "redraw"),
								N(this, "render"),
								n && this.temporaryDisplay(!0),
								h.forEach(function (t) {
									t.call();
								});
						}
						get(t) {
							let e = this.series;
							function i(e) {
								return e.id === t || (e.options && e.options.id === t);
							}
							let s = z(this.axes, i) || z(this.series, i);
							for (let t = 0; !s && t < e.length; t++)
								s = z(e[t].points || [], i);
							return s;
						}
						getAxes() {
							let t = this.userOptions;
							for (let i of (N(this, "getAxes"), ["xAxis", "yAxis"]))
								for (let s of (t[i] = q(t[i] || {}))) new e(this, s, i);
							N(this, "afterGetAxes");
						}
						getSelectedPoints() {
							return this.series.reduce(
								(t, e) => (
									e.getPointsCollection().forEach((e) => {
										V(e.selectedStaging, e.selected) && t.push(e);
									}),
									t
								),
								[]
							);
						}
						getSelectedSeries() {
							return this.series.filter(function (t) {
								return t.selected;
							});
						}
						setTitle(t, e, i) {
							this.applyDescription("title", t),
								this.applyDescription("subtitle", e),
								this.applyDescription("caption", void 0),
								this.layOutTitles(i);
						}
						applyDescription(t, e) {
							let i = this,
								s = (this.options[t] = Y(this.options[t], e)),
								r = this[t];
							r && e && (this[t] = r = r.destroy()),
								s &&
									!r &&
									(((r = this.renderer
										.text(s.text, 0, 0, s.useHTML)
										.attr({
											align: s.align,
											class: "highcharts-" + t,
											zIndex: s.zIndex || 4,
										})
										.add()).update = function (e, s) {
										i.applyDescription(t, e), i.layOutTitles(s);
									}),
									this.styledMode ||
										r.css(
											R(
												"title" === t
													? { fontSize: this.options.isStock ? "1em" : "1.2em" }
													: {},
												s.style
											)
										),
									(this[t] = r));
						}
						layOutTitles(t = !0) {
							let e = [0, 0, 0],
								i = this.renderer,
								s = this.spacingBox;
							["title", "subtitle", "caption"].forEach(function (t) {
								let r = this[t],
									o = this.options[t],
									a = o.verticalAlign || "top",
									n =
										"title" === t
											? "top" === a
												? -3
												: 0
											: "top" === a
											? e[0] + 2
											: 0;
								if (r) {
									r.css({
										width: (o.width || s.width + (o.widthAdjust || 0)) + "px",
									});
									let t = i.fontMetrics(r).b,
										h = Math.round(r.getBBox(o.useHTML).height);
									r.align(
										R({ y: "bottom" === a ? t : n + t, height: h }, o),
										!1,
										"spacingBox"
									),
										o.floating ||
											("top" === a
												? (e[0] = Math.ceil(e[0] + h))
												: "bottom" === a && (e[2] = Math.ceil(e[2] + h)));
								}
							}, this),
								e[0] &&
									"top" === (this.options.title.verticalAlign || "top") &&
									(e[0] += this.options.title.margin),
								e[2] &&
									"bottom" === this.options.caption.verticalAlign &&
									(e[2] += this.options.caption.margin);
							let r =
								!this.titleOffset || this.titleOffset.join(",") !== e.join(",");
							(this.titleOffset = e),
								N(this, "afterLayOutTitles"),
								!this.isDirtyBox &&
									r &&
									((this.isDirtyBox = this.isDirtyLegend = r),
									this.hasRendered && t && this.isDirtyBox && this.redraw());
						}
						getContainerBox() {
							return {
								width: W(this.renderTo, "width", !0) || 0,
								height: W(this.renderTo, "height", !0) || 0,
							};
						}
						getChartSize() {
							let t = this.options.chart,
								e = t.width,
								i = t.height,
								s = this.getContainerBox();
							(this.chartWidth = Math.max(0, e || s.width || 600)),
								(this.chartHeight = Math.max(
									0,
									Z(i, this.chartWidth) || (s.height > 1 ? s.height : 400)
								)),
								(this.containerBox = s);
						}
						temporaryDisplay(t) {
							let e = this.renderTo,
								i;
							if (t)
								for (; e && e.style; )
									e.hcOrigStyle && (O(e, e.hcOrigStyle), delete e.hcOrigStyle),
										e.hcOrigDetached &&
											(k.body.removeChild(e), (e.hcOrigDetached = !1)),
										(e = e.parentNode);
							else
								for (
									;
									e &&
									e.style &&
									(k.body.contains(e) ||
										e.parentNode ||
										((e.hcOrigDetached = !0), k.body.appendChild(e)),
									("none" === W(e, "display", !1) || e.hcOricDetached) &&
										((e.hcOrigStyle = {
											display: e.style.display,
											height: e.style.height,
											overflow: e.style.overflow,
										}),
										(i = { display: "block", overflow: "hidden" }),
										e !== this.renderTo && (i.height = 0),
										O(e, i),
										e.offsetWidth ||
											e.style.setProperty("display", "block", "important")),
									(e = e.parentNode) !== k.body);

								);
						}
						setClassName(t) {
							this.container.className = "highcharts-container " + (t || "");
						}
						getContainer() {
							let t = this.options,
								e = t.chart,
								i = "data-highcharts-chart",
								s = J(),
								r,
								o = this.renderTo;
							o || (this.renderTo = o = e.renderTo),
								F(o) && (this.renderTo = o = k.getElementById(o)),
								o || B(13, !0, this);
							let n = $(P(o, i));
							H(n) && S[n] && S[n].hasRendered && S[n].destroy(),
								P(o, i, this.index),
								(o.innerHTML = p.emptyHTML),
								e.skipClone || o.offsetWidth || this.temporaryDisplay(),
								this.getChartSize();
							let h = this.chartHeight,
								d = this.chartWidth;
							O(o, { overflow: "hidden" }),
								this.styledMode ||
									(r = R(
										{
											position: "relative",
											overflow: "hidden",
											width: d + "px",
											height: h + "px",
											textAlign: "left",
											lineHeight: "normal",
											zIndex: 0,
											"-webkit-tap-highlight-color": "rgba(0,0,0,0)",
											userSelect: "none",
											"touch-action": "manipulation",
											outline: "none",
										},
										e.style || {}
									));
							let c = L("div", { id: s }, r, o);
							(this.container = c),
								this.getChartSize(),
								d === this.chartWidth ||
									((d = this.chartWidth),
									this.styledMode ||
										O(c, { width: V(e.style?.width, d + "px") })),
								(this.containerBox = this.getContainerBox()),
								(this._cursor = c.style.cursor);
							let u = e.renderer || !M ? a.getRendererType(e.renderer) : l;
							if (
								((this.renderer = new u(
									c,
									d,
									h,
									void 0,
									e.forExport,
									t.exporting && t.exporting.allowHTML,
									this.styledMode
								)),
								m(void 0, this),
								this.setClassName(e.className),
								this.styledMode)
							)
								for (let e in t.defs) this.renderer.definition(t.defs[e]);
							else this.renderer.setStyle(e.style);
							(this.renderer.chartIndex = this.index),
								N(this, "afterGetContainer");
						}
						getMargins(t) {
							let { spacing: e, margin: i, titleOffset: s } = this;
							this.resetMargins(),
								s[0] &&
									!D(i[0]) &&
									(this.plotTop = Math.max(this.plotTop, s[0] + e[0])),
								s[2] &&
									!D(i[2]) &&
									(this.marginBottom = Math.max(
										this.marginBottom,
										s[2] + e[2]
									)),
								this.legend &&
									this.legend.display &&
									this.legend.adjustMargins(i, e),
								N(this, "getMargins"),
								t || this.getAxisMargins();
						}
						getAxisMargins() {
							let t = this,
								e = (t.axisOffset = [0, 0, 0, 0]),
								i = t.colorAxis,
								s = t.margin,
								r = function (t) {
									t.forEach(function (t) {
										t.visible && t.getOffset();
									});
								};
							t.hasCartesianSeries ? r(t.axes) : i && i.length && r(i),
								C.forEach(function (i, r) {
									D(s[r]) || (t[i] += e[r]);
								}),
								t.setChartSize();
						}
						getOptions() {
							return E(this.userOptions, x);
						}
						reflow(t) {
							let e = this,
								i = e.containerBox,
								s = e.getContainerBox();
							delete e.pointer?.chartPosition,
								!e.isPrinting &&
									!e.isResizing &&
									i &&
									s.width &&
									((s.width !== i.width || s.height !== i.height) &&
										(c.clearTimeout(e.reflowTimeout),
										(e.reflowTimeout = K(
											function () {
												e.container && e.setSize(void 0, void 0, !1);
											},
											t ? 100 : 0
										))),
									(e.containerBox = s));
						}
						setReflow() {
							let t = this,
								e = (e) => {
									t.options?.chart.reflow && t.hasLoaded && t.reflow(e);
								};
							if ("function" == typeof ResizeObserver)
								new ResizeObserver(e).observe(t.renderTo);
							else {
								let t = A(w, "resize", e);
								A(this, "destroy", t);
							}
						}
						setSize(t, e, i) {
							let s = this,
								r = s.renderer;
							(s.isResizing += 1), m(i, s);
							let o = r.globalAnimation;
							(s.oldChartHeight = s.chartHeight),
								(s.oldChartWidth = s.chartWidth),
								void 0 !== t && (s.options.chart.width = t),
								void 0 !== e && (s.options.chart.height = e),
								s.getChartSize();
							let {
								chartWidth: a,
								chartHeight: n,
								scrollablePixelsX: h = 0,
								scrollablePixelsY: l = 0,
							} = s;
							(s.isDirtyBox ||
								a !== s.oldChartWidth ||
								n !== s.oldChartHeight) &&
								(s.styledMode ||
									(o ? g : O)(
										s.container,
										{ width: `${a + h}px`, height: `${n + l}px` },
										o
									),
								s.setChartSize(!0),
								r.setSize(a, n, o),
								s.axes.forEach(function (t) {
									(t.isDirty = !0), t.setScale();
								}),
								(s.isDirtyLegend = !0),
								(s.isDirtyBox = !0),
								s.layOutTitles(),
								s.getMargins(),
								s.redraw(o),
								(s.oldChartHeight = void 0),
								N(s, "resize"),
								setTimeout(() => {
									s &&
										N(s, "endResize", void 0, () => {
											s.isResizing -= 1;
										});
								}, f(o).duration));
						}
						setChartSize(t) {
							let e, i, s, r;
							let o = this.inverted,
								a = this.renderer,
								n = this.chartWidth,
								h = this.chartHeight,
								l = this.options.chart,
								d = this.spacing,
								c = this.clipOffset;
							(this.plotLeft = e = Math.round(this.plotLeft)),
								(this.plotTop = i = Math.round(this.plotTop)),
								(this.plotWidth = s =
									Math.max(0, Math.round(n - e - this.marginRight))),
								(this.plotHeight = r =
									Math.max(0, Math.round(h - i - this.marginBottom))),
								(this.plotSizeX = o ? r : s),
								(this.plotSizeY = o ? s : r),
								(this.plotBorderWidth = l.plotBorderWidth || 0),
								(this.spacingBox = a.spacingBox =
									{
										x: d[3],
										y: d[0],
										width: n - d[3] - d[1],
										height: h - d[0] - d[2],
									}),
								(this.plotBox = a.plotBox =
									{ x: e, y: i, width: s, height: r });
							let p = 2 * Math.floor(this.plotBorderWidth / 2),
								u = Math.ceil(Math.max(p, c[3]) / 2),
								g = Math.ceil(Math.max(p, c[0]) / 2);
							(this.clipBox = {
								x: u,
								y: g,
								width: Math.floor(this.plotSizeX - Math.max(p, c[1]) / 2 - u),
								height: Math.max(
									0,
									Math.floor(this.plotSizeY - Math.max(p, c[2]) / 2 - g)
								),
							}),
								t ||
									(this.axes.forEach(function (t) {
										t.setAxisSize(), t.setAxisTranslation();
									}),
									a.alignElements()),
								N(this, "afterSetChartSize", { skipAxes: t });
						}
						resetMargins() {
							N(this, "resetMargins");
							let t = this,
								e = t.options.chart;
							["margin", "spacing"].forEach(function (i) {
								let s = e[i],
									r = X(s) ? s : [s, s, s, s];
								["Top", "Right", "Bottom", "Left"].forEach(function (s, o) {
									t[i][o] = V(e[i + s], r[o]);
								});
							}),
								C.forEach(function (e, i) {
									t[e] = V(t.margin[i], t.spacing[i]);
								}),
								(t.axisOffset = [0, 0, 0, 0]),
								(t.clipOffset = [0, 0, 0, 0]);
						}
						drawChartBox() {
							let t = this.options.chart,
								e = this.renderer,
								i = this.chartWidth,
								s = this.chartHeight,
								r = this.styledMode,
								o = this.plotBGImage,
								a = t.backgroundColor,
								n = t.plotBackgroundColor,
								h = t.plotBackgroundImage,
								l = this.plotLeft,
								d = this.plotTop,
								c = this.plotWidth,
								p = this.plotHeight,
								u = this.plotBox,
								g = this.clipRect,
								f = this.clipBox,
								m = this.chartBackground,
								x = this.plotBackground,
								y = this.plotBorder,
								b,
								v,
								S,
								k = "animate";
							m ||
								((this.chartBackground = m =
									e.rect().addClass("highcharts-background").add()),
								(k = "attr")),
								r
									? (b = v = m.strokeWidth())
									: ((v = (b = t.borderWidth || 0) + (t.shadow ? 8 : 0)),
									  (S = { fill: a || "none" }),
									  (b || m["stroke-width"]) &&
											((S.stroke = t.borderColor), (S["stroke-width"] = b)),
									  m.attr(S).shadow(t.shadow)),
								m[k]({
									x: v / 2,
									y: v / 2,
									width: i - v - (b % 2),
									height: s - v - (b % 2),
									r: t.borderRadius,
								}),
								(k = "animate"),
								x ||
									((k = "attr"),
									(this.plotBackground = x =
										e.rect().addClass("highcharts-plot-background").add())),
								x[k](u),
								!r &&
									(x.attr({ fill: n || "none" }).shadow(t.plotShadow),
									h &&
										(o
											? (h !== o.attr("href") && o.attr("href", h),
											  o.animate(u))
											: (this.plotBGImage = e.image(h, l, d, c, p).add()))),
								g
									? g.animate({ width: f.width, height: f.height })
									: (this.clipRect = e.clipRect(f)),
								(k = "animate"),
								y ||
									((k = "attr"),
									(this.plotBorder = y =
										e
											.rect()
											.addClass("highcharts-plot-border")
											.attr({ zIndex: 1 })
											.add())),
								r ||
									y.attr({
										stroke: t.plotBorderColor,
										"stroke-width": t.plotBorderWidth || 0,
										fill: "none",
									}),
								y[k](
									y.crisp({ x: l, y: d, width: c, height: p }, -y.strokeWidth())
								),
								(this.isDirtyBox = !1),
								N(this, "afterDrawChartBox");
						}
						propFromSeries() {
							let t, e, i;
							let s = this,
								r = s.options.chart,
								o = s.options.series;
							["inverted", "angular", "polar"].forEach(function (a) {
								for (
									e = T[r.type],
										i = r[a] || (e && e.prototype[a]),
										t = o && o.length;
									!i && t--;

								)
									(e = T[o[t].type]) && e.prototype[a] && (i = !0);
								s[a] = i;
							});
						}
						linkSeries(t) {
							let e = this,
								i = e.series;
							i.forEach(function (t) {
								t.linkedSeries.length = 0;
							}),
								i.forEach(function (t) {
									let { linkedTo: i } = t.options;
									if (F(i)) {
										let s;
										(s =
											":previous" === i ? e.series[t.index - 1] : e.get(i)) &&
											s.linkedParent !== t &&
											(s.linkedSeries.push(t),
											(t.linkedParent = s),
											s.enabledDataSorting && t.setDataSortingOptions(),
											(t.visible = V(
												t.options.visible,
												s.options.visible,
												t.visible
											)));
									}
								}),
								N(this, "afterLinkSeries", { isUpdating: t });
						}
						renderSeries() {
							this.series.forEach(function (t) {
								t.translate(), t.render();
							});
						}
						render() {
							let t = this.axes,
								e = this.colorAxis,
								i = this.renderer,
								s = this.options.chart.axisLayoutRuns || 2,
								r = (t) => {
									t.forEach((t) => {
										t.visible && t.render();
									});
								},
								o = 0,
								a = !0,
								n,
								h = 0;
							for (let e of (this.setTitle(),
							N(this, "beforeMargins"),
							this.getStacks?.(),
							this.getMargins(!0),
							this.setChartSize(),
							t)) {
								let { options: t } = e,
									{ labels: i } = t;
								if (
									e.horiz &&
									e.visible &&
									i.enabled &&
									e.series.length &&
									"colorAxis" !== e.coll &&
									!this.polar
								) {
									(o = t.tickLength), e.createGroups();
									let s = new u(e, 0, "", !0),
										r = s.createLabel("x", i);
									if (
										(s.destroy(),
										r &&
											V(i.reserveSpace, !H(t.crossing)) &&
											(o =
												r.getBBox().height +
												i.distance +
												Math.max(t.offset || 0, 0)),
										o)
									) {
										r?.destroy();
										break;
									}
								}
							}
							for (
								this.plotHeight = Math.max(this.plotHeight - o, 0);
								(a || n || s > 1) && h < s;

							) {
								let e = this.plotWidth,
									i = this.plotHeight;
								for (let e of t)
									0 === h
										? e.setScale()
										: ((e.horiz && a) || (!e.horiz && n)) &&
										  e.setTickInterval(!0);
								0 === h ? this.getAxisMargins() : this.getMargins(),
									(a = e / this.plotWidth > (h ? 1 : 1.1)),
									(n = i / this.plotHeight > (h ? 1 : 1.05)),
									h++;
							}
							this.drawChartBox(),
								this.hasCartesianSeries ? r(t) : e && e.length && r(e),
								this.seriesGroup ||
									(this.seriesGroup = i
										.g("series-group")
										.attr({ zIndex: 3 })
										.shadow(this.options.chart.seriesGroupShadow)
										.add()),
								this.renderSeries(),
								this.addCredits(),
								this.setResponsive && this.setResponsive(),
								(this.hasRendered = !0);
						}
						addCredits(t) {
							let e = this,
								i = Y(!0, this.options.credits, t);
							i.enabled &&
								!this.credits &&
								((this.credits = this.renderer
									.text(i.text + (this.mapCredits || ""), 0, 0)
									.addClass("highcharts-credits")
									.on("click", function () {
										i.href && (w.location.href = i.href);
									})
									.attr({ align: i.position.align, zIndex: 8 })),
								e.styledMode || this.credits.css(i.style),
								this.credits.add().align(i.position),
								(this.credits.update = function (t) {
									(e.credits = e.credits.destroy()), e.addCredits(t);
								}));
						}
						destroy() {
							let t;
							let e = this,
								i = e.axes,
								s = e.series,
								r = e.container,
								a = r && r.parentNode;
							for (
								N(e, "destroy"),
									e.renderer.forExport ? j(S, e) : (S[e.index] = void 0),
									o.chartCount--,
									e.renderTo.removeAttribute("data-highcharts-chart"),
									_(e),
									t = i.length;
								t--;

							)
								i[t] = i[t].destroy();
							for (
								this.scroller &&
									this.scroller.destroy &&
									this.scroller.destroy(),
									t = s.length;
								t--;

							)
								s[t] = s[t].destroy();
							[
								"title",
								"subtitle",
								"chartBackground",
								"plotBackground",
								"plotBGImage",
								"plotBorder",
								"seriesGroup",
								"clipRect",
								"credits",
								"pointer",
								"rangeSelector",
								"legend",
								"resetZoomButton",
								"tooltip",
								"renderer",
							].forEach(function (t) {
								let i = e[t];
								i && i.destroy && (e[t] = i.destroy());
							}),
								r && ((r.innerHTML = p.emptyHTML), _(r), a && I(r)),
								U(e, function (t, i) {
									delete e[i];
								});
						}
						firstRender() {
							let t = this,
								e = t.options;
							t.getContainer(),
								t.resetMargins(),
								t.setChartSize(),
								t.propFromSeries(),
								t.getAxes();
							let i = G(e.series) ? e.series : [];
							(e.series = []),
								i.forEach(function (e) {
									t.initSeries(e);
								}),
								t.linkSeries(),
								t.setSortedData(),
								N(t, "beforeRender"),
								t.render(),
								t.pointer?.getChartPosition(),
								t.renderer.imgCount || t.hasLoaded || t.onload(),
								t.temporaryDisplay(!0);
						}
						onload() {
							this.callbacks.concat([this.callback]).forEach(function (t) {
								t && void 0 !== this.index && t.apply(this, [this]);
							}, this),
								N(this, "load"),
								N(this, "render"),
								D(this.index) && this.setReflow(),
								this.warnIfA11yModuleNotLoaded(),
								(this.hasLoaded = !0);
						}
						warnIfA11yModuleNotLoaded() {
							let { options: t, title: e } = this;
							!t ||
								this.accessibility ||
								(this.renderer.boxWrapper.attr({
									role: "img",
									"aria-label": ((e && e.element.textContent) || "").replace(
										/</g,
										"&lt;"
									),
								}),
								(t.accessibility && !1 === t.accessibility.enabled) ||
									B(
										'Highcharts warning: Consider including the "accessibility.js" module to make your chart more usable for people with disabilities. Set the "accessibility.enabled" option to false to remove this warning. See https://www.highcharts.com/docs/accessibility/accessibility-module.',
										!1,
										this
									));
						}
						addSeries(t, e, i) {
							let s;
							let r = this;
							return (
								t &&
									((e = V(e, !0)),
									N(r, "addSeries", { options: t }, function () {
										(s = r.initSeries(t)),
											(r.isDirtyLegend = !0),
											r.linkSeries(),
											s.enabledDataSorting && s.setData(t.data, !1),
											N(r, "afterAddSeries", { series: s }),
											e && r.redraw(i);
									})),
								s
							);
						}
						addAxis(t, e, i, s) {
							return this.createAxis(e ? "xAxis" : "yAxis", {
								axis: t,
								redraw: i,
								animation: s,
							});
						}
						addColorAxis(t, e, i) {
							return this.createAxis("colorAxis", {
								axis: t,
								redraw: e,
								animation: i,
							});
						}
						createAxis(t, i) {
							let s = new e(this, i.axis, t);
							return V(i.redraw, !0) && this.redraw(i.animation), s;
						}
						showLoading(t) {
							let e = this,
								i = e.options,
								s = i.loading,
								r = function () {
									o &&
										O(o, {
											left: e.plotLeft + "px",
											top: e.plotTop + "px",
											width: e.plotWidth + "px",
											height: e.plotHeight + "px",
										});
								},
								o = e.loadingDiv,
								a = e.loadingSpan;
							o ||
								(e.loadingDiv = o =
									L(
										"div",
										{
											className: "highcharts-loading highcharts-loading-hidden",
										},
										null,
										e.container
									)),
								a ||
									((e.loadingSpan = a =
										L(
											"span",
											{ className: "highcharts-loading-inner" },
											null,
											o
										)),
									A(e, "redraw", r)),
								(o.className = "highcharts-loading"),
								p.setElementHTML(a, V(t, i.lang.loading, "")),
								e.styledMode ||
									(O(o, R(s.style, { zIndex: 10 })),
									O(a, s.labelStyle),
									e.loadingShown ||
										(O(o, { opacity: 0, display: "" }),
										g(
											o,
											{ opacity: s.style.opacity || 0.5 },
											{ duration: s.showDuration || 0 }
										))),
								(e.loadingShown = !0),
								r();
						}
						hideLoading() {
							let t = this.options,
								e = this.loadingDiv;
							e &&
								((e.className = "highcharts-loading highcharts-loading-hidden"),
								this.styledMode ||
									g(
										e,
										{ opacity: 0 },
										{
											duration: t.loading.hideDuration || 100,
											complete: function () {
												O(e, { display: "none" });
											},
										}
									)),
								(this.loadingShown = !1);
						}
						update(t, e, i, s) {
							let r, o, a;
							let n = this,
								h = {
									credits: "addCredits",
									title: "setTitle",
									subtitle: "setSubtitle",
									caption: "setCaption",
								},
								l = t.isResponsiveOptions,
								c = [];
							N(n, "update", { options: t }),
								l || n.setResponsive(!1, !0),
								(t = E(t, n.options)),
								(n.userOptions = Y(n.userOptions, t));
							let p = t.chart;
							p &&
								(Y(!0, n.options.chart, p),
								this.setZoomOptions(),
								"className" in p && n.setClassName(p.className),
								("inverted" in p || "polar" in p || "type" in p) &&
									(n.propFromSeries(), (r = !0)),
								"alignTicks" in p && (r = !0),
								"events" in p && v(this, p),
								U(p, function (t, e) {
									-1 !== n.propsRequireUpdateSeries.indexOf("chart." + e) &&
										(o = !0),
										-1 !== n.propsRequireDirtyBox.indexOf(e) &&
											(n.isDirtyBox = !0),
										-1 === n.propsRequireReflow.indexOf(e) ||
											((n.isDirtyBox = !0), l || (a = !0));
								}),
								!n.styledMode &&
									p.style &&
									n.renderer.setStyle(n.options.chart.style || {})),
								!n.styledMode && t.colors && (this.options.colors = t.colors),
								t.time &&
									(this.time === y && (this.time = new d(t.time)),
									Y(!0, n.options.time, t.time)),
								U(t, function (e, i) {
									n[i] && "function" == typeof n[i].update
										? n[i].update(e, !1)
										: "function" == typeof n[h[i]]
										? n[h[i]](e)
										: "colors" !== i &&
										  -1 === n.collectionsWithUpdate.indexOf(i) &&
										  Y(!0, n.options[i], t[i]),
										"chart" !== i &&
											-1 !== n.propsRequireUpdateSeries.indexOf(i) &&
											(o = !0);
								}),
								this.collectionsWithUpdate.forEach(function (e) {
									t[e] &&
										(q(t[e]).forEach(function (t, s) {
											let r;
											let o = D(t.id);
											o && (r = n.get(t.id)),
												!r &&
													n[e] &&
													(r = n[e][V(t.index, s)]) &&
													((o && D(r.options.id)) || r.options.isInternal) &&
													(r = void 0),
												r &&
													r.coll === e &&
													(r.update(t, !1), i && (r.touched = !0)),
												!r &&
													i &&
													n.collectionsWithInit[e] &&
													(n.collectionsWithInit[e][0].apply(
														n,
														[t]
															.concat(n.collectionsWithInit[e][1] || [])
															.concat([!1])
													).touched = !0);
										}),
										i &&
											n[e].forEach(function (t) {
												t.touched || t.options.isInternal
													? delete t.touched
													: c.push(t);
											}));
								}),
								c.forEach(function (t) {
									t.chart && t.remove && t.remove(!1);
								}),
								r &&
									n.axes.forEach(function (t) {
										t.update({}, !1);
									}),
								o &&
									n.getSeriesOrderByLinks().forEach(function (t) {
										t.chart && t.update({}, !1);
									}, this);
							let u = p && p.width,
								g =
									p &&
									(F(p.height) ? Z(p.height, u || n.chartWidth) : p.height);
							a || (H(u) && u !== n.chartWidth) || (H(g) && g !== n.chartHeight)
								? n.setSize(u, g, s)
								: V(e, !0) && n.redraw(s),
								N(n, "afterUpdate", { options: t, redraw: e, animation: s });
						}
						setSubtitle(t, e) {
							this.applyDescription("subtitle", t), this.layOutTitles(e);
						}
						setCaption(t, e) {
							this.applyDescription("caption", t), this.layOutTitles(e);
						}
						showResetZoom() {
							let t = this,
								e = x.lang,
								i = t.zooming.resetButton,
								s = i.theme,
								r =
									"chart" === i.relativeTo || "spacingBox" === i.relativeTo
										? null
										: "plotBox";
							function o() {
								t.zoomOut();
							}
							N(this, "beforeShowResetZoom", null, function () {
								t.resetZoomButton = t.renderer
									.button(e.resetZoom, null, null, o, s)
									.attr({ align: i.position.align, title: e.resetZoomTitle })
									.addClass("highcharts-reset-zoom")
									.add()
									.align(i.position, !1, r);
							}),
								N(this, "afterShowResetZoom");
						}
						zoomOut() {
							N(this, "selection", { resetSelection: !0 }, () =>
								this.transform({ reset: !0, trigger: "zoom" })
							);
						}
						pan(t, e) {
							let i = this,
								s = "object" == typeof e ? e : { enabled: e, type: "x" },
								r = s.type,
								o =
									r &&
									i[{ x: "xAxis", xy: "axes", y: "yAxis" }[r]].filter(
										(t) => t.options.panningEnabled && !t.options.isInternal
									),
								a = i.options.chart;
							a?.panning && (a.panning = s),
								N(this, "pan", { originalEvent: t }, () => {
									i.transform({
										axes: o,
										event: t,
										to: {
											x: t.chartX - (i.mouseDownX || 0),
											y: t.chartY - (i.mouseDownY || 0),
										},
										trigger: "pan",
									}),
										O(i.container, { cursor: "move" });
								});
						}
						transform(t) {
							let {
									axes: e = this.axes,
									event: i,
									from: s = {},
									reset: r,
									selection: o,
									to: a = {},
									trigger: n,
								} = t,
								{ inverted: h, resetZoomButton: l } = this,
								d = !1,
								c;
							for (let t of (this.hoverPoints?.forEach((t) => t.setState()),
							e)) {
								let {
										horiz: e,
										len: l,
										minPointOffset: p = 0,
										options: u,
										reversed: g,
									} = t,
									f = e ? "width" : "height",
									m = e ? "x" : "y",
									x = a[f] || t.len,
									y = s[f] || t.len,
									b = 10 > Math.abs(x) ? 1 : x / y,
									v = (s[m] || 0) + y / 2 - t.pos,
									S = v - ((a[m] ?? t.pos) + x / 2 - t.pos) / b,
									k = (g && !h) || (!g && h) ? -1 : 1;
								if (!r && (v < 0 || v > t.len)) continue;
								let C = t.toValue(S, !0) + p * k,
									M = t.toValue(S + l / b, !0) - (p * k || 0),
									w = t.allExtremes;
								if (
									(C > M && ([C, M] = [M, C]),
									1 === b && !r && "yAxis" === t.coll && !w)
								) {
									for (let e of t.series) {
										let t = e.getExtremes(e.getProcessedData(!0).yData, !0);
										w ??
											(w = {
												dataMin: Number.MAX_VALUE,
												dataMax: -Number.MAX_VALUE,
											}),
											H(t.dataMin) &&
												H(t.dataMax) &&
												((w.dataMin = Math.min(t.dataMin, w.dataMin)),
												(w.dataMax = Math.max(t.dataMax, w.dataMax)));
									}
									t.allExtremes = w;
								}
								let {
										dataMin: T,
										dataMax: A,
										min: P,
										max: L,
									} = R(t.getExtremes(), w || {}),
									O = T ?? u.min,
									E = A ?? u.max,
									I = M - C,
									j = t.categories ? 0 : Math.min(I, E - O),
									B = O - j * (D(u.min) ? 0 : u.minPadding),
									z = E + j * (D(u.max) ? 0 : u.maxPadding),
									N = t.allowZoomOutside || 1 === b || ("zoom" !== n && b > 1),
									W = Math.min(u.min ?? B, B, N ? P : B),
									G = Math.max(u.max ?? z, z, N ? L : z);
								(!t.isOrdinal || 1 !== b || r) &&
									(C < W && ((C = W), b >= 1 && (M = C + I)),
									M > G && ((M = G), b >= 1 && (C = M - I)),
									(r ||
										(t.series.length &&
											(C !== P || M !== L) &&
											C >= W &&
											M <= G)) &&
										(o
											? o[t.coll].push({ axis: t, min: C, max: M })
											: ((t.isPanning = "zoom" !== n),
											  t.setExtremes(r ? void 0 : C, r ? void 0 : M, !1, !1, {
													move: S,
													trigger: n,
													scale: b,
											  }),
											  !r &&
													(C > W || M < G) &&
													"mousewheel" !== n &&
													(c = !0)),
										(d = !0)),
									i &&
										(this[e ? "mouseDownX" : "mouseDownY"] =
											i[e ? "chartX" : "chartY"]));
							}
							return (
								d &&
									(o
										? N(this, "selection", o, () => {
												delete t.selection,
													(t.trigger = "zoom"),
													this.transform(t);
										  })
										: (c && !l
												? this.showResetZoom()
												: !c && l && (this.resetZoomButton = l.destroy()),
										  this.redraw(
												"zoom" === n &&
													(this.options.chart.animation ??
														this.pointCount < 100)
										  ))),
								d
							);
						}
					}
					return (
						R(Q.prototype, {
							callbacks: [],
							collectionsWithInit: {
								xAxis: [Q.prototype.addAxis, [!0]],
								yAxis: [Q.prototype.addAxis, [!1]],
								series: [Q.prototype.addSeries],
							},
							collectionsWithUpdate: ["xAxis", "yAxis", "series"],
							propsRequireDirtyBox: [
								"backgroundColor",
								"borderColor",
								"borderWidth",
								"borderRadius",
								"plotBackgroundColor",
								"plotBackgroundImage",
								"plotBorderColor",
								"plotBorderWidth",
								"plotShadow",
								"shadow",
							],
							propsRequireReflow: [
								"margin",
								"marginTop",
								"marginRight",
								"marginBottom",
								"marginLeft",
								"spacing",
								"spacingTop",
								"spacingRight",
								"spacingBottom",
								"spacingLeft",
							],
							propsRequireUpdateSeries: [
								"chart.inverted",
								"chart.polar",
								"chart.ignoreHiddenSeries",
								"chart.type",
								"colors",
								"plotOptions",
								"time",
								"tooltip",
							],
						}),
						Q
					);
				}
			),
			i(
				e,
				"Extensions/ScrollablePlotArea.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Globals.js"],
					e["Core/Renderer/RendererRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s) {
					let { stop: r } = t,
						{ composed: o } = e,
						{
							addEvent: a,
							createElement: n,
							css: h,
							defined: l,
							merge: d,
							pushUnique: c,
						} = s;
					function p() {
						let t = this.scrollablePlotArea;
						(this.scrollablePixelsX || this.scrollablePixelsY) &&
							!t &&
							(this.scrollablePlotArea = t = new g(this)),
							t?.applyFixed();
					}
					function u() {
						this.chart.scrollablePlotArea &&
							(this.chart.scrollablePlotArea.isDirty = !0);
					}
					class g {
						static compose(t, e, i) {
							c(o, this.compose) &&
								(a(t, "afterInit", u),
								a(e, "afterSetChartSize", (t) =>
									this.afterSetSize(t.target, t)
								),
								a(e, "render", p),
								a(i, "show", u));
						}
						static afterSetSize(t, e) {
							let i, s, r;
							let { minWidth: o, minHeight: a } =
									t.options.chart.scrollablePlotArea || {},
								{ clipBox: n, plotBox: h, inverted: c, renderer: p } = t;
							if (
								!p.forExport &&
								(o
									? ((t.scrollablePixelsX = i = Math.max(0, o - t.chartWidth)),
									  i &&
											((t.scrollablePlotBox = d(t.plotBox)),
											(h.width = t.plotWidth += i),
											(n[c ? "height" : "width"] += i),
											(r = !0)))
									: a &&
									  ((t.scrollablePixelsY = s = Math.max(0, a - t.chartHeight)),
									  l(s) &&
											((t.scrollablePlotBox = d(t.plotBox)),
											(h.height = t.plotHeight += s),
											(n[c ? "width" : "height"] += s),
											(r = !1))),
								l(r) && !e.skipAxes)
							)
								for (let e of t.axes)
									e.horiz === r && (e.setAxisSize(), e.setAxisTranslation());
						}
						constructor(t) {
							let e;
							let s = t.options.chart,
								r = i.getRendererType(),
								o = s.scrollablePlotArea || {},
								l = this.moveFixedElements.bind(this),
								d = {
									WebkitOverflowScrolling: "touch",
									overflowX: "hidden",
									overflowY: "hidden",
								};
							t.scrollablePixelsX && (d.overflowX = "auto"),
								t.scrollablePixelsY && (d.overflowY = "auto"),
								(this.chart = t);
							let c = (this.parentDiv = n(
									"div",
									{ className: "highcharts-scrolling-parent" },
									{ position: "relative" },
									t.renderTo
								)),
								p = (this.scrollingContainer = n(
									"div",
									{ className: "highcharts-scrolling" },
									d,
									c
								)),
								u = (this.innerContainer = n(
									"div",
									{ className: "highcharts-inner-container" },
									void 0,
									p
								)),
								g = (this.fixedDiv = n(
									"div",
									{ className: "highcharts-fixed" },
									{
										position: "absolute",
										overflow: "hidden",
										pointerEvents: "none",
										zIndex: (s.style?.zIndex || 0) + 2,
										top: 0,
									},
									void 0,
									!0
								)),
								f = (this.fixedRenderer = new r(
									g,
									t.chartWidth,
									t.chartHeight,
									s.style
								));
							(this.mask = f
								.path()
								.attr({
									fill: s.backgroundColor || "#fff",
									"fill-opacity": o.opacity ?? 0.85,
									zIndex: -1,
								})
								.addClass("highcharts-scrollable-mask")
								.add()),
								p.parentNode.insertBefore(g, p),
								h(t.renderTo, { overflow: "visible" }),
								a(t, "afterShowResetZoom", l),
								a(t, "afterApplyDrilldown", l),
								a(t, "afterLayOutTitles", l),
								a(p, "scroll", () => {
									let { pointer: i, hoverPoint: s } = t;
									i &&
										(delete i.chartPosition,
										s && (e = s),
										i.runPointActions(void 0, e, !0));
								}),
								u.appendChild(t.container);
						}
						applyFixed() {
							let {
									chart: t,
									fixedRenderer: e,
									isDirty: i,
									scrollingContainer: s,
								} = this,
								{
									axisOffset: o,
									chartWidth: a,
									chartHeight: n,
									container: d,
									plotHeight: c,
									plotLeft: p,
									plotTop: u,
									plotWidth: g,
									scrollablePixelsX: f = 0,
									scrollablePixelsY: m = 0,
								} = t,
								{ scrollPositionX: x = 0, scrollPositionY: y = 0 } =
									t.options.chart.scrollablePlotArea || {},
								b = a + f,
								v = n + m;
							e.setSize(a, n),
								(i ?? !0) && ((this.isDirty = !1), this.moveFixedElements()),
								r(t.container),
								h(d, { width: `${b}px`, height: `${v}px` }),
								t.renderer.boxWrapper.attr({
									width: b,
									height: v,
									viewBox: [0, 0, b, v].join(" "),
								}),
								t.chartBackground?.attr({ width: b, height: v }),
								h(s, { width: `${a}px`, height: `${n}px` }),
								l(i) || ((s.scrollLeft = f * x), (s.scrollTop = m * y));
							let S = u - o[0] - 1,
								k = p - o[3] - 1,
								C = u + c + o[2] + 1,
								M = p + g + o[1] + 1,
								w = p + g - f,
								T = u + c - m,
								A = [["M", 0, 0]];
							f
								? (A = [
										["M", 0, S],
										["L", p - 1, S],
										["L", p - 1, C],
										["L", 0, C],
										["Z"],
										["M", w, S],
										["L", a, S],
										["L", a, C],
										["L", w, C],
										["Z"],
								  ])
								: m &&
								  (A = [
										["M", k, 0],
										["L", k, u - 1],
										["L", M, u - 1],
										["L", M, 0],
										["Z"],
										["M", k, T],
										["L", k, n],
										["L", M, n],
										["L", M, T],
										["Z"],
								  ]),
								"adjustHeight" !== t.redrawTrigger && this.mask.attr({ d: A });
						}
						moveFixedElements() {
							let t;
							let {
									container: e,
									inverted: i,
									scrollablePixelsX: s,
									scrollablePixelsY: r,
								} = this.chart,
								o = this.fixedRenderer,
								a = [
									".highcharts-breadcrumbs-group",
									".highcharts-contextbutton",
									".highcharts-caption",
									".highcharts-credits",
									".highcharts-legend",
									".highcharts-legend-checkbox",
									".highcharts-navigator-series",
									".highcharts-navigator-xaxis",
									".highcharts-navigator-yaxis",
									".highcharts-navigator",
									".highcharts-reset-zoom",
									".highcharts-drillup-button",
									".highcharts-scrollbar",
									".highcharts-subtitle",
									".highcharts-title",
								];
							for (let n of (s && !i
								? (t = ".highcharts-yaxis")
								: s && i
								? (t = ".highcharts-xaxis")
								: r && !i
								? (t = ".highcharts-xaxis")
								: r && i && (t = ".highcharts-yaxis"),
							t &&
								a.push(
									`${t}:not(.highcharts-radial-axis)`,
									`${t}-labels:not(.highcharts-radial-axis-labels)`
								),
							a))
								[].forEach.call(e.querySelectorAll(n), (t) => {
									(t.namespaceURI === o.SVG_NS
										? o.box
										: o.box.parentNode
									).appendChild(t),
										(t.style.pointerEvents = "auto");
								});
						}
					}
					return g;
				}
			),
			i(
				e,
				"Core/Axis/Stacking/StackItem.js",
				[
					e["Core/Templating.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					let { format: s } = t,
						{ series: r } = e,
						{
							destroyObjectProperties: o,
							fireEvent: a,
							isNumber: n,
							pick: h,
						} = i;
					return class {
						constructor(t, e, i, s, r) {
							let o = t.chart.inverted,
								a = t.reversed;
							this.axis = t;
							let n = (this.isNegative = !!i != !!a);
							(this.options = e = e || {}),
								(this.x = s),
								(this.total = null),
								(this.cumulative = null),
								(this.points = {}),
								(this.hasValidPoints = !1),
								(this.stack = r),
								(this.leftCliff = 0),
								(this.rightCliff = 0),
								(this.alignOptions = {
									align: e.align || (o ? (n ? "left" : "right") : "center"),
									verticalAlign:
										e.verticalAlign || (o ? "middle" : n ? "bottom" : "top"),
									y: e.y,
									x: e.x,
								}),
								(this.textAlign =
									e.textAlign || (o ? (n ? "right" : "left") : "center"));
						}
						destroy() {
							o(this, this.axis);
						}
						render(t) {
							let e = this.axis.chart,
								i = this.options,
								r = i.format,
								o = r ? s(r, this, e) : i.formatter.call(this);
							if (this.label)
								this.label.attr({ text: o, visibility: "hidden" });
							else {
								this.label = e.renderer.label(
									o,
									null,
									void 0,
									i.shape,
									void 0,
									void 0,
									i.useHTML,
									!1,
									"stack-labels"
								);
								let s = {
									r: i.borderRadius || 0,
									text: o,
									padding: h(i.padding, 5),
									visibility: "hidden",
								};
								e.styledMode ||
									((s.fill = i.backgroundColor),
									(s.stroke = i.borderColor),
									(s["stroke-width"] = i.borderWidth),
									this.label.css(i.style || {})),
									this.label.attr(s),
									this.label.added || this.label.add(t);
							}
							(this.label.labelrank = e.plotSizeY), a(this, "afterRender");
						}
						setOffset(t, e, i, s, o, l) {
							let {
									alignOptions: d,
									axis: c,
									label: p,
									options: u,
									textAlign: g,
								} = this,
								f = c.chart,
								m = this.getStackBox({
									xOffset: t,
									width: e,
									boxBottom: i,
									boxTop: s,
									defaultX: o,
									xAxis: l,
								}),
								{ verticalAlign: x } = d;
							if (p && m) {
								let t = p.getBBox(void 0, 0),
									e = p.padding,
									i = "justify" === h(u.overflow, "justify"),
									s;
								(d.x = u.x || 0), (d.y = u.y || 0);
								let { x: o, y: a } = this.adjustStackPosition({
									labelBox: t,
									verticalAlign: x,
									textAlign: g,
								});
								(m.x -= o),
									(m.y -= a),
									p.align(d, !1, m),
									(s = f.isInsidePlot(
										p.alignAttr.x + d.x + o,
										p.alignAttr.y + d.y + a
									)) || (i = !1),
									i &&
										r.prototype.justifyDataLabel.call(
											c,
											p,
											d,
											p.alignAttr,
											t,
											m
										),
									p.attr({
										x: p.alignAttr.x,
										y: p.alignAttr.y,
										rotation: u.rotation,
										rotationOriginX:
											t.width *
											{ left: 0, center: 0.5, right: 1 }[
												u.textAlign || "center"
											],
										rotationOriginY: t.height / 2,
									}),
									h(!i && u.crop, !0) &&
										(s =
											n(p.x) &&
											n(p.y) &&
											f.isInsidePlot(p.x - e + (p.width || 0), p.y) &&
											f.isInsidePlot(p.x + e, p.y)),
									p[s ? "show" : "hide"]();
							}
							a(this, "afterSetOffset", { xOffset: t, width: e });
						}
						adjustStackPosition({
							labelBox: t,
							verticalAlign: e,
							textAlign: i,
						}) {
							let s = {
									bottom: 0,
									middle: 1,
									top: 2,
									right: 1,
									center: 0,
									left: -1,
								},
								r = s[e],
								o = s[i];
							return {
								x: t.width / 2 + (t.width / 2) * o,
								y: (t.height / 2) * r,
							};
						}
						getStackBox(t) {
							let e = this.axis,
								i = e.chart,
								{
									boxTop: s,
									defaultX: r,
									xOffset: o,
									width: a,
									boxBottom: l,
								} = t,
								d = e.stacking.usePercentage ? 100 : h(s, this.total, 0),
								c = e.toPixels(d),
								p = t.xAxis || i.xAxis[0],
								u = h(r, p.translate(this.x)) + o,
								g = Math.abs(
									c -
										e.toPixels(
											l ||
												(n(e.min) &&
													e.logarithmic &&
													e.logarithmic.lin2log(e.min)) ||
												0
										)
								),
								f = i.inverted,
								m = this.isNegative;
							return f
								? {
										x: (m ? c : c - g) - i.plotLeft,
										y: p.height - u - a,
										width: g,
										height: a,
								  }
								: {
										x: u + p.transB - i.plotLeft,
										y: (m ? c - g : c) - i.plotTop,
										width: a,
										height: g,
								  };
						}
					};
				}
			),
			i(
				e,
				"Core/Axis/Stacking/StackingAxis.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Axis/Axis.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Axis/Stacking/StackItem.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r) {
					var o;
					let { getDeferredAnimation: a } = t,
						{
							series: { prototype: n },
						} = i,
						{
							addEvent: h,
							correctFloat: l,
							defined: d,
							destroyObjectProperties: c,
							fireEvent: p,
							isArray: u,
							isNumber: g,
							objectEach: f,
							pick: m,
						} = r;
					function x() {
						let t = this.inverted;
						this.axes.forEach((t) => {
							t.stacking &&
								t.stacking.stacks &&
								t.hasVisibleSeries &&
								(t.stacking.oldStacks = t.stacking.stacks);
						}),
							this.series.forEach((e) => {
								let i = (e.xAxis && e.xAxis.options) || {};
								e.options.stacking &&
									e.reserveSpace() &&
									(e.stackKey = [
										e.type,
										m(e.options.stack, ""),
										t ? i.top : i.left,
										t ? i.height : i.width,
									].join(","));
							});
					}
					function y() {
						let t = this.stacking;
						if (t) {
							let e = t.stacks;
							f(e, (t, i) => {
								c(t), delete e[i];
							}),
								t.stackTotalGroup?.destroy();
						}
					}
					function b() {
						this.stacking || (this.stacking = new w(this));
					}
					function v(t, e, i, s) {
						return (
							!d(t) || t.x !== e || (s && t.stackKey !== s)
								? (t = { x: e, index: 0, key: s, stackKey: s })
								: t.index++,
							(t.key = [i, e, t.index].join(",")),
							t
						);
					}
					function S() {
						let t;
						let e = this,
							i = e.yAxis,
							s = e.stackKey || "",
							r = i.stacking.stacks,
							o = e.processedXData,
							a = e.options.stacking,
							n = e[a + "Stacker"];
						n &&
							[s, "-" + s].forEach((i) => {
								let s = o.length,
									a,
									h,
									l;
								for (; s--; )
									(a = o[s]),
										(t = e.getStackIndicator(t, a, e.index, i)),
										(h = r[i]?.[a]),
										(l = h?.points[t.key || ""]) && n.call(e, l, h, s);
							});
					}
					function k(t, e, i) {
						let s = e.total ? 100 / e.total : 0;
						(t[0] = l(t[0] * s)),
							(t[1] = l(t[1] * s)),
							(this.stackedYData[i] = t[1]);
					}
					function C(t) {
						(this.is("column") || this.is("columnrange")) &&
							(this.options.centerInCategory &&
							!this.options.stacking &&
							this.chart.series.length > 1
								? n.setStackedPoints.call(this, t, "group")
								: t.stacking.resetStacks());
					}
					function M(t, e) {
						let i, r, o, a, n, h, c, p, g;
						let f = e || this.options.stacking;
						if (
							!f ||
							!this.reserveSpace() ||
							({ group: "xAxis" }[f] || "yAxis") !== t.coll
						)
							return;
						let x = this.processedXData,
							y = this.processedYData,
							b = [],
							v = y.length,
							S = this.options,
							k = S.threshold || 0,
							C = S.startFromThreshold ? k : 0,
							M = S.stack,
							w = e ? `${this.type},${f}` : this.stackKey || "",
							T = "-" + w,
							A = this.negStacks,
							P = t.stacking,
							L = P.stacks,
							O = P.oldStacks;
						for (P.stacksTouched += 1, c = 0; c < v; c++) {
							(p = x[c]),
								(g = y[c]),
								(h = (i = this.getStackIndicator(i, p, this.index)).key || ""),
								L[(n = (r = A && g < (C ? 0 : k)) ? T : w)] || (L[n] = {}),
								L[n][p] ||
									(O[n]?.[p]
										? ((L[n][p] = O[n][p]), (L[n][p].total = null))
										: (L[n][p] = new s(t, t.options.stackLabels, !!r, p, M))),
								(o = L[n][p]),
								null !== g
									? ((o.points[h] = o.points[this.index] =
											[m(o.cumulative, C)]),
									  d(o.cumulative) || (o.base = h),
									  (o.touched = P.stacksTouched),
									  i.index > 0 &&
											!1 === this.singleStacks &&
											(o.points[h][0] =
												o.points[this.index + "," + p + ",0"][0]))
									: (delete o.points[h], delete o.points[this.index]);
							let e = o.total || 0;
							"percent" === f
								? ((a = r ? w : T),
								  (e =
										A && L[a]?.[p]
											? ((a = L[a][p]).total =
													Math.max(a.total || 0, e) + Math.abs(g) || 0)
											: l(e + (Math.abs(g) || 0))))
								: "group" === f
								? (u(g) && (g = g[0]), null !== g && e++)
								: (e = l(e + (g || 0))),
								"group" === f
									? (o.cumulative = (e || 1) - 1)
									: (o.cumulative = l(m(o.cumulative, C) + (g || 0))),
								(o.total = e),
								null !== g &&
									(o.points[h].push(o.cumulative),
									(b[c] = o.cumulative),
									(o.hasValidPoints = !0));
						}
						"percent" === f && (P.usePercentage = !0),
							"group" !== f && (this.stackedYData = b),
							(P.oldStacks = {});
					}
					class w {
						constructor(t) {
							(this.oldStacks = {}),
								(this.stacks = {}),
								(this.stacksTouched = 0),
								(this.axis = t);
						}
						buildStacks() {
							let t, e;
							let i = this.axis,
								s = i.series,
								r = "xAxis" === i.coll,
								o = i.options.reversedStacks,
								a = s.length;
							for (this.resetStacks(), this.usePercentage = !1, e = a; e--; )
								(t = s[o ? e : a - e - 1]),
									r && t.setGroupedPoints(i),
									t.setStackedPoints(i);
							if (!r) for (e = 0; e < a; e++) s[e].modifyStacks();
							p(i, "afterBuildStacks");
						}
						cleanStacks() {
							this.oldStacks &&
								((this.stacks = this.oldStacks),
								f(this.stacks, (t) => {
									f(t, (t) => {
										t.cumulative = t.total;
									});
								}));
						}
						resetStacks() {
							f(this.stacks, (t) => {
								f(t, (e, i) => {
									g(e.touched) && e.touched < this.stacksTouched
										? (e.destroy(), delete t[i])
										: ((e.total = null), (e.cumulative = null));
								});
							});
						}
						renderStackTotals() {
							let t = this.axis,
								e = t.chart,
								i = e.renderer,
								s = this.stacks,
								r = a(e, t.options.stackLabels?.animation || !1),
								o = (this.stackTotalGroup =
									this.stackTotalGroup ||
									i.g("stack-labels").attr({ zIndex: 6, opacity: 0 }).add());
							o.translate(e.plotLeft, e.plotTop),
								f(s, (t) => {
									f(t, (t) => {
										t.render(o);
									});
								}),
								o.animate({ opacity: 1 }, r);
						}
					}
					return (
						((o || (o = {})).compose = function (t, e, i) {
							let s = e.prototype,
								r = i.prototype;
							s.getStacks ||
								(h(t, "init", b),
								h(t, "destroy", y),
								(s.getStacks = x),
								(r.getStackIndicator = v),
								(r.modifyStacks = S),
								(r.percentStacker = k),
								(r.setGroupedPoints = C),
								(r.setStackedPoints = M));
						}),
						o
					);
				}
			),
			i(
				e,
				"Series/Line/LineSeries.js",
				[
					e["Core/Series/Series.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					let { defined: s, merge: r, isObject: o } = i;
					class a extends t {
						drawGraph() {
							let t = this.options,
								e = (this.gappedPath || this.getGraphPath).call(this),
								i = this.chart.styledMode;
							[this, ...this.zones].forEach((s, a) => {
								let n,
									h = s.graph,
									l = h ? "animate" : "attr",
									d = s.dashStyle || t.dashStyle;
								h
									? ((h.endX = this.preventGraphAnimation ? null : e.xMap),
									  h.animate({ d: e }))
									: e.length &&
									  (s.graph = h =
											this.chart.renderer
												.path(e)
												.addClass(
													"highcharts-graph" +
														(a ? ` highcharts-zone-graph-${a - 1} ` : " ") +
														((a && s.className) || "")
												)
												.attr({ zIndex: 1 })
												.add(this.group)),
									h &&
										!i &&
										((n = {
											stroke:
												(!a && t.lineColor) ||
												s.color ||
												this.color ||
												"#cccccc",
											"stroke-width": t.lineWidth || 0,
											fill: (this.fillGraph && this.color) || "none",
										}),
										d
											? (n.dashstyle = d)
											: "square" !== t.linecap &&
											  (n["stroke-linecap"] = n["stroke-linejoin"] = "round"),
										h[l](n).shadow(
											a < 2 &&
												t.shadow &&
												r(
													{ filterUnits: "userSpaceOnUse" },
													o(t.shadow) ? t.shadow : {}
												)
										)),
									h && ((h.startX = e.xMap), (h.isArea = e.isArea));
							});
						}
						getGraphPath(t, e, i) {
							let r = this,
								o = r.options,
								a = [],
								n = [],
								h,
								l = o.step,
								d = (t = t || r.points).reversed;
							return (
								d && t.reverse(),
								(l = { right: 1, center: 2 }[l] || (l && 3)) &&
									d &&
									(l = 4 - l),
								(t = this.getValidPoints(
									t,
									!1,
									!(o.connectNulls && !e && !i)
								)).forEach(function (d, c) {
									let p;
									let u = d.plotX,
										g = d.plotY,
										f = t[c - 1],
										m = d.isNull || "number" != typeof g;
									(d.leftCliff || (f && f.rightCliff)) && !i && (h = !0),
										m && !s(e) && c > 0
											? (h = !o.connectNulls)
											: m && !e
											? (h = !0)
											: (0 === c || h
													? (p = [["M", d.plotX, d.plotY]])
													: r.getPointSpline
													? (p = [r.getPointSpline(t, d, c)])
													: l
													? (p =
															1 === l
																? [["L", f.plotX, g]]
																: 2 === l
																? [
																		["L", (f.plotX + u) / 2, f.plotY],
																		["L", (f.plotX + u) / 2, g],
																  ]
																: [["L", u, f.plotY]]).push(["L", u, g])
													: (p = [["L", u, g]]),
											  n.push(d.x),
											  l && (n.push(d.x), 2 === l && n.push(d.x)),
											  a.push.apply(a, p),
											  (h = !1));
								}),
								(a.xMap = n),
								(r.graphPath = a),
								a
							);
						}
					}
					return (
						(a.defaultOptions = r(t.defaultOptions, {
							legendSymbol: "lineMarker",
						})),
						e.registerSeriesType("line", a),
						a
					);
				}
			),
			i(
				e,
				"Series/Area/AreaSeries.js",
				[e["Core/Series/SeriesRegistry.js"], e["Core/Utilities.js"]],
				function (t, e) {
					let {
							seriesTypes: { line: i },
						} = t,
						{ extend: s, merge: r, objectEach: o, pick: a } = e;
					class n extends i {
						drawGraph() {
							(this.areaPath = []), super.drawGraph.apply(this);
							let { areaPath: t, options: e } = this;
							[this, ...this.zones].forEach((i, s) => {
								let r = {},
									o = i.fillColor || e.fillColor,
									a = i.area,
									n = a ? "animate" : "attr";
								a
									? ((a.endX = this.preventGraphAnimation ? null : t.xMap),
									  a.animate({ d: t }))
									: ((r.zIndex = 0),
									  ((a = i.area =
											this.chart.renderer
												.path(t)
												.addClass(
													"highcharts-area" +
														(s ? ` highcharts-zone-area-${s - 1} ` : " ") +
														((s && i.className) || "")
												)
												.add(this.group)).isArea = !0)),
									this.chart.styledMode ||
										((r.fill = o || i.color || this.color),
										(r["fill-opacity"] = o ? 1 : e.fillOpacity ?? 0.75),
										a.css({
											pointerEvents: this.stickyTracking ? "none" : "auto",
										})),
									a[n](r),
									(a.startX = t.xMap),
									(a.shiftUnit = e.step ? 2 : 1);
							});
						}
						getGraphPath(t) {
							let e, s, r;
							let o = i.prototype.getGraphPath,
								n = this.options,
								h = n.stacking,
								l = this.yAxis,
								d = [],
								c = [],
								p = this.index,
								u = l.stacking.stacks[this.stackKey],
								g = n.threshold,
								f = Math.round(l.getThreshold(n.threshold)),
								m = a(n.connectNulls, "percent" === h),
								x = function (i, s, r) {
									let o = t[i],
										a = h && u[o.x].points[p],
										n = o[r + "Null"] || 0,
										m = o[r + "Cliff"] || 0,
										x,
										y,
										b = !0;
									m || n
										? ((x = (n ? a[0] : a[1]) + m), (y = a[0] + m), (b = !!n))
										: !h && t[s] && t[s].isNull && (x = y = g),
										void 0 !== x &&
											(c.push({
												plotX: e,
												plotY: null === x ? f : l.getThreshold(x),
												isNull: b,
												isCliff: !0,
											}),
											d.push({
												plotX: e,
												plotY: null === y ? f : l.getThreshold(y),
												doCurve: !1,
											}));
								};
							(t = t || this.points), h && (t = this.getStackPoints(t));
							for (let i = 0, o = t.length; i < o; ++i)
								h ||
									(t[i].leftCliff =
										t[i].rightCliff =
										t[i].leftNull =
										t[i].rightNull =
											void 0),
									(s = t[i].isNull),
									(e = a(t[i].rectPlotX, t[i].plotX)),
									(r = h ? a(t[i].yBottom, f) : f),
									(s && !m) ||
										(m || x(i, i - 1, "left"),
										(s && !h && m) ||
											(c.push(t[i]), d.push({ x: i, plotX: e, plotY: r })),
										m || x(i, i + 1, "right"));
							let y = o.call(this, c, !0, !0);
							d.reversed = !0;
							let b = o.call(this, d, !0, !0),
								v = b[0];
							v && "M" === v[0] && (b[0] = ["L", v[1], v[2]]);
							let S = y.concat(b);
							S.length && S.push(["Z"]);
							let k = o.call(this, c, !1, m);
							return (S.xMap = y.xMap), (this.areaPath = S), k;
						}
						getStackPoints(t) {
							let e = this,
								i = [],
								s = [],
								r = this.xAxis,
								n = this.yAxis,
								h = n.stacking.stacks[this.stackKey],
								l = {},
								d = n.series,
								c = d.length,
								p = n.options.reversedStacks ? 1 : -1,
								u = d.indexOf(e);
							if (((t = t || this.points), this.options.stacking)) {
								for (let e = 0; e < t.length; e++)
									(t[e].leftNull = t[e].rightNull = void 0), (l[t[e].x] = t[e]);
								o(h, function (t, e) {
									null !== t.total && s.push(e);
								}),
									s.sort(function (t, e) {
										return t - e;
									});
								let g = d.map((t) => t.visible);
								s.forEach(function (t, o) {
									let f = 0,
										m,
										x;
									if (l[t] && !l[t].isNull)
										i.push(l[t]),
											[-1, 1].forEach(function (i) {
												let r = 1 === i ? "rightNull" : "leftNull",
													a = h[s[o + i]],
													n = 0;
												if (a) {
													let i = u;
													for (; i >= 0 && i < c; ) {
														let s = d[i].index;
														!(m = a.points[s]) &&
															(s === e.index
																? (l[t][r] = !0)
																: g[i] &&
																  (x = h[t].points[s]) &&
																  (n -= x[1] - x[0])),
															(i += p);
													}
												}
												l[t][1 === i ? "rightCliff" : "leftCliff"] = n;
											});
									else {
										let e = u;
										for (; e >= 0 && e < c; ) {
											let i = d[e].index;
											if ((m = h[t].points[i])) {
												f = m[1];
												break;
											}
											e += p;
										}
										(f = a(f, 0)),
											(f = n.translate(f, 0, 1, 0, 1)),
											i.push({
												isNull: !0,
												plotX: r.translate(t, 0, 0, 0, 1),
												x: t,
												plotY: f,
												yBottom: f,
											});
									}
								});
							}
							return i;
						}
					}
					return (
						(n.defaultOptions = r(i.defaultOptions, {
							threshold: 0,
							legendSymbol: "areaMarker",
						})),
						s(n.prototype, { singleStacks: !1 }),
						t.registerSeriesType("area", n),
						n
					);
				}
			),
			i(
				e,
				"Series/Spline/SplineSeries.js",
				[e["Core/Series/SeriesRegistry.js"], e["Core/Utilities.js"]],
				function (t, e) {
					let { line: i } = t.seriesTypes,
						{ merge: s, pick: r } = e;
					class o extends i {
						getPointSpline(t, e, i) {
							let s, o, a, n;
							let h = e.plotX || 0,
								l = e.plotY || 0,
								d = t[i - 1],
								c = t[i + 1];
							function p(t) {
								return t && !t.isNull && !1 !== t.doCurve && !e.isCliff;
							}
							if (p(d) && p(c)) {
								let t = d.plotX || 0,
									i = d.plotY || 0,
									r = c.plotX || 0,
									p = c.plotY || 0,
									u = 0;
								(s = (1.5 * h + t) / 2.5),
									(o = (1.5 * l + i) / 2.5),
									(a = (1.5 * h + r) / 2.5),
									(n = (1.5 * l + p) / 2.5),
									a !== s && (u = ((n - o) * (a - h)) / (a - s) + l - n),
									(o += u),
									(n += u),
									o > i && o > l
										? ((o = Math.max(i, l)), (n = 2 * l - o))
										: o < i && o < l && ((o = Math.min(i, l)), (n = 2 * l - o)),
									n > p && n > l
										? ((n = Math.max(p, l)), (o = 2 * l - n))
										: n < p && n < l && ((n = Math.min(p, l)), (o = 2 * l - n)),
									(e.rightContX = a),
									(e.rightContY = n),
									(e.controlPoints = { low: [s, o], high: [a, n] });
							}
							let u = [
								"C",
								r(d.rightContX, d.plotX, 0),
								r(d.rightContY, d.plotY, 0),
								r(s, h, 0),
								r(o, l, 0),
								h,
								l,
							];
							return (d.rightContX = d.rightContY = void 0), u;
						}
					}
					return (
						(o.defaultOptions = s(i.defaultOptions)),
						t.registerSeriesType("spline", o),
						o
					);
				}
			),
			i(
				e,
				"Series/AreaSpline/AreaSplineSeries.js",
				[
					e["Series/Spline/SplineSeries.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					let {
							area: s,
							area: { prototype: r },
						} = e.seriesTypes,
						{ extend: o, merge: a } = i;
					class n extends t {}
					return (
						(n.defaultOptions = a(t.defaultOptions, s.defaultOptions)),
						o(n.prototype, {
							getGraphPath: r.getGraphPath,
							getStackPoints: r.getStackPoints,
							drawGraph: r.drawGraph,
						}),
						e.registerSeriesType("areaspline", n),
						n
					);
				}
			),
			i(e, "Series/Column/ColumnSeriesDefaults.js", [], function () {
				return {
					borderRadius: 3,
					centerInCategory: !1,
					groupPadding: 0.2,
					marker: null,
					pointPadding: 0.1,
					minPointLength: 0,
					cropThreshold: 50,
					pointRange: null,
					states: {
						hover: { halo: !1, brightness: 0.1 },
						select: { color: "#cccccc", borderColor: "#000000" },
					},
					dataLabels: { align: void 0, verticalAlign: void 0, y: void 0 },
					startFromThreshold: !0,
					stickyTracking: !1,
					tooltip: { distance: 6 },
					threshold: 0,
					borderColor: "#ffffff",
				};
			}),
			i(
				e,
				"Series/Column/ColumnSeries.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Color/Color.js"],
					e["Series/Column/ColumnSeriesDefaults.js"],
					e["Core/Globals.js"],
					e["Core/Series/Series.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r, o, a) {
					let { animObject: n } = t,
						{ parse: h } = e,
						{ noop: l } = s,
						{
							clamp: d,
							defined: c,
							extend: p,
							fireEvent: u,
							isArray: g,
							isNumber: f,
							merge: m,
							pick: x,
							objectEach: y,
						} = a;
					class b extends r {
						animate(t) {
							let e, i;
							let s = this,
								r = this.yAxis,
								o = r.pos,
								a = s.options,
								h = this.chart.inverted,
								l = {},
								c = h ? "translateX" : "translateY";
							t
								? ((l.scaleY = 0.001),
								  (i = d(r.toPixels(a.threshold), o, o + r.len)),
								  h ? (l.translateX = i - r.len) : (l.translateY = i),
								  s.clipBox && s.setClip(),
								  s.group.attr(l))
								: ((e = Number(s.group.attr(c))),
								  s.group.animate(
										{ scaleY: 1 },
										p(n(s.options.animation), {
											step: function (t, i) {
												s.group &&
													((l[c] = e + i.pos * (o - e)), s.group.attr(l));
											},
										})
								  ));
						}
						init(t, e) {
							super.init.apply(this, arguments);
							let i = this;
							(t = i.chart).hasRendered &&
								t.series.forEach(function (t) {
									t.type === i.type && (t.isDirty = !0);
								});
						}
						getColumnMetrics() {
							let t = this,
								e = t.options,
								i = t.xAxis,
								s = t.yAxis,
								r = i.options.reversedStacks,
								o = (i.reversed && !r) || (!i.reversed && r),
								a = {},
								n,
								h = 0;
							!1 === e.grouping
								? (h = 1)
								: t.chart.series.forEach(function (e) {
										let i;
										let r = e.yAxis,
											o = e.options;
										e.type === t.type &&
											e.reserveSpace() &&
											s.len === r.len &&
											s.pos === r.pos &&
											(o.stacking && "group" !== o.stacking
												? (void 0 === a[(n = e.stackKey)] && (a[n] = h++),
												  (i = a[n]))
												: !1 !== o.grouping && (i = h++),
											(e.columnIndex = i));
								  });
							let l = Math.min(
									Math.abs(i.transA) *
										((!i.brokenAxis?.hasBreaks && i.ordinal?.slope) ||
											e.pointRange ||
											i.closestPointRange ||
											i.tickInterval ||
											1),
									i.len
								),
								d = l * e.groupPadding,
								c = (l - 2 * d) / (h || 1),
								p = Math.min(
									e.maxPointWidth || i.len,
									x(e.pointWidth, c * (1 - 2 * e.pointPadding))
								),
								u = (t.columnIndex || 0) + (o ? 1 : 0);
							return (
								(t.columnMetrics = {
									width: p,
									offset: (c - p) / 2 + (d + u * c - l / 2) * (o ? -1 : 1),
									paddedWidth: c,
									columnCount: h,
								}),
								t.columnMetrics
							);
						}
						crispCol(t, e, i, s) {
							let r = this.borderWidth,
								o = -(r % 2 ? 0.5 : 0),
								a = r % 2 ? 0.5 : 1;
							this.options.crisp &&
								(i = Math.round(t + i) + o - (t = Math.round(t) + o));
							let n = Math.round(e + s) + a,
								h = 0.5 >= Math.abs(e) && n > 0.5;
							return (
								(s = n - (e = Math.round(e) + a)),
								h && s && ((e -= 1), (s += 1)),
								{ x: t, y: e, width: i, height: s }
							);
						}
						adjustForMissingColumns(t, e, i, s) {
							if (!i.isNull && s.columnCount > 1) {
								let r = this.xAxis.series
										.filter((t) => t.visible)
										.map((t) => t.index),
									o = 0,
									a = 0;
								y(this.xAxis.stacking?.stacks, (t) => {
									if ("number" == typeof i.x) {
										let e = t[i.x.toString()];
										if (e && g(e.points[this.index])) {
											let t = Object.keys(e.points)
												.filter(
													(t) =>
														!t.match(",") &&
														e.points[t] &&
														e.points[t].length > 1
												)
												.map(parseFloat)
												.filter((t) => -1 !== r.indexOf(t))
												.sort((t, e) => e - t);
											(o = t.indexOf(this.index)), (a = t.length);
										}
									}
								}),
									(o = this.xAxis.reversed ? a - 1 - o : o);
								let n = (a - 1) * s.paddedWidth + e;
								t = (i.plotX || 0) + n / 2 - e - o * s.paddedWidth;
							}
							return t;
						}
						translate() {
							let t = this,
								e = t.chart,
								i = t.options,
								s = (t.dense = t.closestPointRange * t.xAxis.transA < 2),
								o = (t.borderWidth = x(i.borderWidth, s ? 0 : 1)),
								a = t.xAxis,
								n = t.yAxis,
								h = i.threshold,
								l = x(i.minPointLength, 5),
								p = t.getColumnMetrics(),
								g = p.width,
								m = (t.pointXOffset = p.offset),
								y = t.dataMin,
								b = t.dataMax,
								v = (t.barW = Math.max(g, 1 + 2 * o)),
								S = (t.translatedThreshold = n.getThreshold(h));
							e.inverted && (S -= 0.5),
								i.pointPadding && (v = Math.ceil(v)),
								r.prototype.translate.apply(t),
								t.points.forEach(function (s) {
									let r = x(s.yBottom, S),
										o = 999 + Math.abs(r),
										u = s.plotX || 0,
										k = d(s.plotY, -o, n.len + o),
										C,
										M = Math.min(k, r),
										w = Math.max(k, r) - M,
										T = g,
										A = u + m,
										P = v;
									l &&
										Math.abs(w) < l &&
										((w = l),
										(C =
											(!n.reversed && !s.negative) ||
											(n.reversed && s.negative)),
										f(h) &&
											f(b) &&
											s.y === h &&
											b <= h &&
											(n.min || 0) < h &&
											(y !== b || (n.max || 0) <= h) &&
											((C = !C), (s.negative = !s.negative)),
										(M = Math.abs(M - S) > l ? r - l : S - (C ? l : 0))),
										c(s.options.pointWidth) &&
											(A -= Math.round(
												((T = P = Math.ceil(s.options.pointWidth)) - g) / 2
											)),
										i.centerInCategory &&
											!i.stacking &&
											(A = t.adjustForMissingColumns(A, T, s, p)),
										(s.barX = A),
										(s.pointWidth = T),
										(s.tooltipPos = e.inverted
											? [
													d(
														n.len + n.pos - e.plotLeft - k,
														n.pos - e.plotLeft,
														n.len + n.pos - e.plotLeft
													),
													a.len + a.pos - e.plotTop - A - P / 2,
													w,
											  ]
											: [
													a.left - e.plotLeft + A + P / 2,
													d(
														k + n.pos - e.plotTop,
														n.pos - e.plotTop,
														n.len + n.pos - e.plotTop
													),
													w,
											  ]),
										(s.shapeType =
											t.pointClass.prototype.shapeType || "roundedRect"),
										(s.shapeArgs = t.crispCol(
											A,
											s.isNull ? S : M,
											P,
											s.isNull ? 0 : w
										));
								}),
								u(this, "afterColumnTranslate");
						}
						drawGraph() {
							this.group[this.dense ? "addClass" : "removeClass"](
								"highcharts-dense-data"
							);
						}
						pointAttribs(t, e) {
							let i = this.options,
								s = this.pointAttrToOptions || {},
								r = s.stroke || "borderColor",
								o = s["stroke-width"] || "borderWidth",
								a,
								n,
								l,
								d = (t && t.color) || this.color,
								c = (t && t[r]) || i[r] || d,
								p = (t && t.options.dashStyle) || i.dashStyle,
								u = (t && t[o]) || i[o] || this[o] || 0,
								g = x(t && t.opacity, i.opacity, 1);
							t &&
								this.zones.length &&
								((n = t.getZone()),
								(d =
									t.options.color ||
									(n && (n.color || t.nonZonedColor)) ||
									this.color),
								n &&
									((c = n.borderColor || c),
									(p = n.dashStyle || p),
									(u = n.borderWidth || u))),
								e &&
									t &&
									((l = (a = m(
										i.states[e],
										(t.options.states && t.options.states[e]) || {}
									)).brightness),
									(d =
										a.color ||
										(void 0 !== l && h(d).brighten(a.brightness).get()) ||
										d),
									(c = a[r] || c),
									(u = a[o] || u),
									(p = a.dashStyle || p),
									(g = x(a.opacity, g)));
							let f = { fill: d, stroke: c, "stroke-width": u, opacity: g };
							return p && (f.dashstyle = p), f;
						}
						drawPoints(t = this.points) {
							let e;
							let i = this,
								s = this.chart,
								r = i.options,
								o = s.renderer,
								a = r.animationLimit || 250;
							t.forEach(function (t) {
								let n = t.plotY,
									h = t.graphic,
									l = !!h,
									d = h && s.pointCount < a ? "animate" : "attr";
								f(n) && null !== t.y
									? ((e = t.shapeArgs),
									  h && t.hasNewShapeType() && (h = h.destroy()),
									  i.enabledDataSorting &&
											(t.startXPos = i.xAxis.reversed
												? -((e && e.width) || 0)
												: i.xAxis.width),
									  !h &&
											((t.graphic = h =
												o[t.shapeType](e).add(t.group || i.group)),
											h &&
												i.enabledDataSorting &&
												s.hasRendered &&
												s.pointCount < a &&
												(h.attr({ x: t.startXPos }),
												(l = !0),
												(d = "animate"))),
									  h && l && h[d](m(e)),
									  s.styledMode ||
											h[d](i.pointAttribs(t, t.selected && "select")).shadow(
												!1 !== t.allowShadow && r.shadow
											),
									  h &&
											(h.addClass(t.getClassName(), !0),
											h.attr({ visibility: t.visible ? "inherit" : "hidden" })))
									: h && (t.graphic = h.destroy());
							});
						}
						drawTracker(t = this.points) {
							let e;
							let i = this,
								s = i.chart,
								r = s.pointer,
								o = function (t) {
									let e = r?.getPointFromEvent(t);
									r &&
										e &&
										i.options.enableMouseTracking &&
										((r.isDirectTouch = !0), e.onMouseOver(t));
								};
							t.forEach(function (t) {
								(e = g(t.dataLabels)
									? t.dataLabels
									: t.dataLabel
									? [t.dataLabel]
									: []),
									t.graphic && (t.graphic.element.point = t),
									e.forEach(function (e) {
										(e.div || e.element).point = t;
									});
							}),
								i._hasTracking ||
									(i.trackerGroups.forEach(function (t) {
										i[t] &&
											(i[t]
												.addClass("highcharts-tracker")
												.on("mouseover", o)
												.on("mouseout", function (t) {
													r?.onTrackerMouseOut(t);
												})
												.on("touchstart", o),
											!s.styledMode &&
												i.options.cursor &&
												i[t].css({ cursor: i.options.cursor }));
									}),
									(i._hasTracking = !0)),
								u(this, "afterDrawTracker");
						}
						remove() {
							let t = this,
								e = t.chart;
							e.hasRendered &&
								e.series.forEach(function (e) {
									e.type === t.type && (e.isDirty = !0);
								}),
								r.prototype.remove.apply(t, arguments);
						}
					}
					return (
						(b.defaultOptions = m(r.defaultOptions, i)),
						p(b.prototype, {
							directTouch: !0,
							getSymbol: l,
							negStacks: !0,
							trackerGroups: ["group", "dataLabelsGroup"],
						}),
						o.registerSeriesType("column", b),
						b
					);
				}
			),
			i(
				e,
				"Core/Series/DataLabel.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Templating.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					var s;
					let { getDeferredAnimation: r } = t,
						{ format: o } = e,
						{
							defined: a,
							extend: n,
							fireEvent: h,
							isArray: l,
							isString: d,
							merge: c,
							objectEach: p,
							pick: u,
							pInt: g,
							splat: f,
						} = i;
					return (
						(function (t) {
							function e() {
								return v(this).some((t) => t?.enabled);
							}
							function i(t, e, i, s, r) {
								let o = this.chart,
									h = this.isCartesian && o.inverted,
									l = this.enabledDataSorting,
									d = t.plotX,
									p = t.plotY,
									g = i.rotation || 0,
									f =
										a(d) &&
										a(p) &&
										o.isInsidePlot(d, Math.round(p), {
											inverted: h,
											paneCoordinates: !0,
											series: this,
										}),
									m =
										0 === g &&
										"justify" === u(i.overflow, l ? "none" : "justify"),
									x =
										this.visible &&
										!1 !== t.visible &&
										a(d) &&
										(t.series.forceDL ||
											(l && !m) ||
											f ||
											(u(i.inside, !!this.options.stacking) &&
												s &&
												o.isInsidePlot(d, h ? s.x + 1 : s.y + s.height - 1, {
													inverted: h,
													paneCoordinates: !0,
													series: this,
												}))),
									y = t.pos();
								if (x && y) {
									var b;
									let a = e.getBBox(),
										h = e.getBBox(void 0, 0),
										d = { right: 1, center: 0.5 }[i.align || 0] || 0,
										p = { bottom: 1, middle: 0.5 }[i.verticalAlign || 0] || 0;
									if (
										((s = n(
											{ x: y[0], y: Math.round(y[1]), width: 0, height: 0 },
											s || {}
										)),
										n(i, { width: a.width, height: a.height }),
										(b = s),
										l &&
											this.xAxis &&
											!m &&
											this.setDataLabelStartPos(t, e, r, f, b),
										e.align(
											c(i, { width: h.width, height: h.height }),
											!1,
											s,
											!1
										),
										(e.alignAttr.x += d * (h.width - a.width)),
										(e.alignAttr.y += p * (h.height - a.height)),
										e[e.placed ? "animate" : "attr"]({
											x: e.alignAttr.x + (a.width - h.width) / 2,
											y: e.alignAttr.y + (a.height - h.height) / 2,
											rotationOriginX: (e.width || 0) / 2,
											rotationOriginY: (e.height || 0) / 2,
										}),
										m && s.height >= 0)
									)
										this.justifyDataLabel(e, i, e.alignAttr, a, s, r);
									else if (u(i.crop, !0)) {
										let { x: t, y: i } = e.alignAttr;
										x =
											o.isInsidePlot(t, i, {
												paneCoordinates: !0,
												series: this,
											}) &&
											o.isInsidePlot(t + a.width - 1, i + a.height - 1, {
												paneCoordinates: !0,
												series: this,
											});
									}
									i.shape &&
										!g &&
										e[r ? "attr" : "animate"]({ anchorX: y[0], anchorY: y[1] });
								}
								r && l && (e.placed = !1),
									x || (l && !m)
										? (e.show(), (e.placed = !0))
										: (e.hide(), (e.placed = !1));
							}
							function s() {
								return this.plotGroup(
									"dataLabelsGroup",
									"data-labels",
									this.hasRendered ? "inherit" : "hidden",
									this.options.dataLabels.zIndex || 6
								);
							}
							function m(t) {
								let e = this.hasRendered || 0,
									i = this.initDataLabelsGroup().attr({ opacity: +e });
								return (
									!e &&
										i &&
										(this.visible && i.show(),
										this.options.animation
											? i.animate({ opacity: 1 }, t)
											: i.attr({ opacity: 1 })),
									i
								);
							}
							function x(t) {
								let e;
								t = t || this.points;
								let i = this,
									s = i.chart,
									n = i.options,
									l = s.renderer,
									{ backgroundColor: c, plotBackgroundColor: m } =
										s.options.chart,
									x = l.getContrast((d(m) && m) || (d(c) && c) || "#000000"),
									y = v(i),
									{ animation: S, defer: k } = y[0],
									C = k ? r(s, S, i) : { defer: 0, duration: 0 };
								h(this, "drawDataLabels"),
									i.hasDataLabels?.() &&
										((e = this.initDataLabels(C)),
										t.forEach((t) => {
											let r = t.dataLabels || [];
											f(b(y, t.dlOptions || t.options?.dataLabels)).forEach(
												(h, c) => {
													let f =
															h.enabled &&
															(t.visible || t.dataLabelOnHidden) &&
															(!t.isNull || t.dataLabelOnNull) &&
															(function (t, e) {
																let i = e.filter;
																if (i) {
																	let e = i.operator,
																		s = t[i.property],
																		r = i.value;
																	return (
																		(">" === e && s > r) ||
																		("<" === e && s < r) ||
																		(">=" === e && s >= r) ||
																		("<=" === e && s <= r) ||
																		("==" === e && s == r) ||
																		("===" === e && s === r) ||
																		("!=" === e && s != r) ||
																		("!==" === e && s !== r)
																	);
																}
																return !0;
															})(t, h),
														{
															backgroundColor: m,
															borderColor: y,
															distance: b,
															style: v = {},
														} = h,
														S,
														k,
														C,
														M,
														w = {},
														T = r[c],
														A = !T,
														P;
													if (
														(f &&
															((k = u(h[t.formatPrefix + "Format"], h.format)),
															(S = t.getLabelConfig()),
															(C = a(k)
																? o(k, S, s)
																: (
																		h[t.formatPrefix + "Formatter"] ||
																		h.formatter
																  ).call(S, h)),
															(M = h.rotation),
															!s.styledMode &&
																((v.color = u(
																	h.color,
																	v.color,
																	d(i.color) ? i.color : void 0,
																	"#000000"
																)),
																"contrast" === v.color
																	? ("none" !== m && (P = m),
																	  (t.contrastColor = l.getContrast(
																			("auto" !== P && P) || t.color || i.color
																	  )),
																	  (v.color =
																			P ||
																			(!a(b) && h.inside) ||
																			0 > g(b || 0) ||
																			n.stacking
																				? t.contrastColor
																				: x))
																	: delete t.contrastColor,
																n.cursor && (v.cursor = n.cursor)),
															(w = {
																r: h.borderRadius || 0,
																rotation: M,
																padding: h.padding,
																zIndex: 1,
															}),
															s.styledMode ||
																((w.fill = "auto" === m ? t.color : m),
																(w.stroke = "auto" === y ? t.color : y),
																(w["stroke-width"] = h.borderWidth)),
															p(w, (t, e) => {
																void 0 === t && delete w[e];
															})),
														!T ||
															(f &&
																a(C) &&
																!!T.div == !!h.useHTML &&
																((T.rotation && h.rotation) ||
																	T.rotation === h.rotation)) ||
															((T = void 0), (A = !0)),
														f &&
															a(C) &&
															(T
																? (w.text = C)
																: (T = l.label(
																		C,
																		0,
																		0,
																		h.shape,
																		void 0,
																		void 0,
																		h.useHTML,
																		void 0,
																		"data-label"
																  )).addClass(
																		" highcharts-data-label-color-" +
																			t.colorIndex +
																			" " +
																			(h.className || "") +
																			(h.useHTML ? " highcharts-tracker" : "")
																  ),
															T))
													) {
														(T.options = h),
															T.attr(w),
															s.styledMode || T.css(v).shadow(h.shadow);
														let o =
															h[t.formatPrefix + "TextPath"] || h.textPath;
														o &&
															!h.useHTML &&
															(T.setTextPath(
																t.getDataLabelPath?.(T) || t.graphic,
																o
															),
															t.dataLabelPath &&
																!o.enabled &&
																(t.dataLabelPath = t.dataLabelPath.destroy())),
															T.added || T.add(e),
															i.alignDataLabel(t, T, h, void 0, A),
															(T.isActive = !0),
															r[c] && r[c] !== T && r[c].destroy(),
															(r[c] = T);
													}
												}
											);
											let h = r.length;
											for (; h--; )
												r[h] && r[h].isActive
													? (r[h].isActive = !1)
													: (r[h]?.destroy(), r.splice(h, 1));
											(t.dataLabel = r[0]), (t.dataLabels = r);
										})),
									h(this, "afterDrawDataLabels");
							}
							function y(t, e, i, s, r, o) {
								let a = this.chart,
									n = e.align,
									h = e.verticalAlign,
									l = t.box ? 0 : t.padding || 0,
									{ x: d = 0, y: c = 0 } = e,
									p,
									u;
								return (
									(p = (i.x || 0) + l) < 0 &&
										("right" === n && d >= 0
											? ((e.align = "left"), (e.inside = !0))
											: (d -= p),
										(u = !0)),
									(p = (i.x || 0) + s.width - l) > a.plotWidth &&
										("left" === n && d <= 0
											? ((e.align = "right"), (e.inside = !0))
											: (d += a.plotWidth - p),
										(u = !0)),
									(p = i.y + l) < 0 &&
										("bottom" === h && c >= 0
											? ((e.verticalAlign = "top"), (e.inside = !0))
											: (c -= p),
										(u = !0)),
									(p = (i.y || 0) + s.height - l) > a.plotHeight &&
										("top" === h && c <= 0
											? ((e.verticalAlign = "bottom"), (e.inside = !0))
											: (c += a.plotHeight - p),
										(u = !0)),
									u &&
										((e.x = d),
										(e.y = c),
										(t.placed = !o),
										t.align(e, void 0, r)),
									u
								);
							}
							function b(t, e) {
								let i = [],
									s;
								if (l(t) && !l(e))
									i = t.map(function (t) {
										return c(t, e);
									});
								else if (l(e) && !l(t))
									i = e.map(function (e) {
										return c(t, e);
									});
								else if (l(t) || l(e)) {
									if (l(t) && l(e))
										for (s = Math.max(t.length, e.length); s--; )
											i[s] = c(t[s], e[s]);
								} else i = c(t, e);
								return i;
							}
							function v(t) {
								let e = t.chart.options.plotOptions;
								return f(
									b(
										b(e?.series?.dataLabels, e?.[t.type]?.dataLabels),
										t.options.dataLabels
									)
								);
							}
							function S(t, e, i, s, r) {
								let o = this.chart,
									a = o.inverted,
									n = this.xAxis,
									h = n.reversed,
									l = ((a ? e.height : e.width) || 0) / 2,
									d = t.pointWidth,
									c = d ? d / 2 : 0;
								(e.startXPos = a ? r.x : h ? -l - c : n.width - l + c),
									(e.startYPos = a
										? h
											? this.yAxis.height - l + c
											: -l - c
										: r.y),
									s
										? "hidden" === e.visibility &&
										  (e.show(), e.attr({ opacity: 0 }).animate({ opacity: 1 }))
										: e
												.attr({ opacity: 1 })
												.animate({ opacity: 0 }, void 0, e.hide),
									o.hasRendered &&
										(i && e.attr({ x: e.startXPos, y: e.startYPos }),
										(e.placed = !0));
							}
							t.compose = function (t) {
								let r = t.prototype;
								r.initDataLabels ||
									((r.initDataLabels = m),
									(r.initDataLabelsGroup = s),
									(r.alignDataLabel = i),
									(r.drawDataLabels = x),
									(r.justifyDataLabel = y),
									(r.setDataLabelStartPos = S),
									(r.hasDataLabels = e));
							};
						})(s || (s = {})),
						s
					);
				}
			),
			i(
				e,
				"Series/Column/ColumnDataLabel.js",
				[
					e["Core/Series/DataLabel.js"],
					e["Core/Globals.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s) {
					var r;
					let { composed: o } = e,
						{ series: a } = i,
						{ merge: n, pick: h, pushUnique: l } = s;
					return (
						(function (e) {
							function i(t, e, i, s, r) {
								let o = this.chart.inverted,
									l = t.series,
									d = (l.xAxis ? l.xAxis.len : this.chart.plotSizeX) || 0,
									c = (l.yAxis ? l.yAxis.len : this.chart.plotSizeY) || 0,
									p = t.dlBox || t.shapeArgs,
									u = h(t.below, t.plotY > h(this.translatedThreshold, c)),
									g = h(i.inside, !!this.options.stacking);
								if (p) {
									if (
										((s = n(p)), !("allow" === i.overflow && !1 === i.crop))
									) {
										s.y < 0 && ((s.height += s.y), (s.y = 0));
										let t = s.y + s.height - c;
										t > 0 && t < s.height && (s.height -= t);
									}
									o &&
										(s = {
											x: c - s.y - s.height,
											y: d - s.x - s.width,
											width: s.height,
											height: s.width,
										}),
										g ||
											(o
												? ((s.x += u ? 0 : s.width), (s.width = 0))
												: ((s.y += u ? s.height : 0), (s.height = 0)));
								}
								(i.align = h(
									i.align,
									!o || g ? "center" : u ? "right" : "left"
								)),
									(i.verticalAlign = h(
										i.verticalAlign,
										o || g ? "middle" : u ? "top" : "bottom"
									)),
									a.prototype.alignDataLabel.call(this, t, e, i, s, r),
									i.inside &&
										t.contrastColor &&
										e.css({ color: t.contrastColor });
							}
							e.compose = function (e) {
								t.compose(a),
									l(o, "ColumnDataLabel") && (e.prototype.alignDataLabel = i);
							};
						})(r || (r = {})),
						r
					);
				}
			),
			i(
				e,
				"Series/Bar/BarSeries.js",
				[
					e["Series/Column/ColumnSeries.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					let { extend: s, merge: r } = i;
					class o extends t {}
					return (
						(o.defaultOptions = r(t.defaultOptions, {})),
						s(o.prototype, { inverted: !0 }),
						e.registerSeriesType("bar", o),
						o
					);
				}
			),
			i(e, "Series/Scatter/ScatterSeriesDefaults.js", [], function () {
				return {
					lineWidth: 0,
					findNearestPointBy: "xy",
					jitter: { x: 0, y: 0 },
					marker: { enabled: !0 },
					tooltip: {
						headerFormat:
							'<span style="color:{point.color}">●</span> <span style="font-size: 0.8em"> {series.name}</span><br/>',
						pointFormat: "x: <b>{point.x}</b><br/>y: <b>{point.y}</b><br/>",
					},
				};
			}),
			i(
				e,
				"Series/Scatter/ScatterSeries.js",
				[
					e["Series/Scatter/ScatterSeriesDefaults.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					let { column: s, line: r } = e.seriesTypes,
						{ addEvent: o, extend: a, merge: n } = i;
					class h extends r {
						applyJitter() {
							let t = this,
								e = this.options.jitter,
								i = this.points.length;
							e &&
								this.points.forEach(function (s, r) {
									["x", "y"].forEach(function (o, a) {
										let n,
											h = "plot" + o.toUpperCase(),
											l,
											d,
											c;
										e[o] &&
											!s.isNull &&
											((n = t[o + "Axis"]),
											(c = e[o] * n.transA),
											n &&
												!n.isLog &&
												((l = Math.max(0, s[h] - c)),
												(d = Math.min(n.len, s[h] + c)),
												(s[h] =
													l +
													(d - l) *
														(function (t) {
															let e = 1e4 * Math.sin(t);
															return e - Math.floor(e);
														})(r + a * i)),
												"x" === o && (s.clientX = s.plotX)));
									});
								});
						}
						drawGraph() {
							this.options.lineWidth
								? super.drawGraph()
								: this.graph && (this.graph = this.graph.destroy());
						}
					}
					return (
						(h.defaultOptions = n(r.defaultOptions, t)),
						a(h.prototype, {
							drawTracker: s.prototype.drawTracker,
							sorted: !1,
							requireSorting: !1,
							noSharedTooltip: !0,
							trackerGroups: ["group", "markerGroup", "dataLabelsGroup"],
						}),
						o(h, "afterTranslate", function () {
							this.applyJitter();
						}),
						e.registerSeriesType("scatter", h),
						h
					);
				}
			),
			i(
				e,
				"Series/CenteredUtilities.js",
				[
					e["Core/Globals.js"],
					e["Core/Series/Series.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					var s, r;
					let { deg2rad: o } = t,
						{ fireEvent: a, isNumber: n, pick: h, relativeLength: l } = i;
					return (
						((r = s || (s = {})).getCenter = function () {
							let t = this.options,
								i = this.chart,
								s = 2 * (t.slicedOffset || 0),
								r = i.plotWidth - 2 * s,
								o = i.plotHeight - 2 * s,
								d = t.center,
								c = Math.min(r, o),
								p = t.thickness,
								u,
								g = t.size,
								f = t.innerSize || 0,
								m,
								x;
							"string" == typeof g && (g = parseFloat(g)),
								"string" == typeof f && (f = parseFloat(f));
							let y = [
								h(d[0], "50%"),
								h(d[1], "50%"),
								h(g && g < 0 ? void 0 : t.size, "100%"),
								h(f && f < 0 ? void 0 : t.innerSize || 0, "0%"),
							];
							for (
								!i.angular || this instanceof e || (y[3] = 0), m = 0;
								m < 4;
								++m
							)
								(x = y[m]),
									(u = m < 2 || (2 === m && /%$/.test(x))),
									(y[m] = l(x, [r, o, c, y[2]][m]) + (u ? s : 0));
							return (
								y[3] > y[2] && (y[3] = y[2]),
								n(p) && 2 * p < y[2] && p > 0 && (y[3] = y[2] - 2 * p),
								a(this, "afterGetCenter", { positions: y }),
								y
							);
						}),
						(r.getStartAndEndRadians = function (t, e) {
							let i = n(t) ? t : 0,
								s = n(e) && e > i && e - i < 360 ? e : i + 360;
							return { start: o * (i + -90), end: o * (s + -90) };
						}),
						s
					);
				}
			),
			i(
				e,
				"Series/Pie/PiePoint.js",
				[
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Series/Point.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i) {
					let { setAnimation: s } = t,
						{
							addEvent: r,
							defined: o,
							extend: a,
							isNumber: n,
							pick: h,
							relativeLength: l,
						} = i;
					class d extends e {
						getConnectorPath(t) {
							let e = t.dataLabelPosition,
								i = t.options || {},
								s = i.connectorShape,
								r = this.connectorShapes[s] || s;
							return (
								(e &&
									r.call(
										this,
										{ ...e.computed, alignment: e.alignment },
										e.connectorPosition,
										i
									)) ||
								[]
							);
						}
						getTranslate() {
							return (
								(this.sliced && this.slicedTranslation) || {
									translateX: 0,
									translateY: 0,
								}
							);
						}
						haloPath(t) {
							let e = this.shapeArgs;
							return this.sliced || !this.visible
								? []
								: this.series.chart.renderer.symbols.arc(
										e.x,
										e.y,
										e.r + t,
										e.r + t,
										{
											innerR: e.r - 1,
											start: e.start,
											end: e.end,
											borderRadius: e.borderRadius,
										}
								  );
						}
						constructor(t, e, i) {
							super(t, e, i),
								(this.half = 0),
								this.name ?? (this.name = "Slice");
							let s = (t) => {
								this.slice("select" === t.type);
							};
							r(this, "select", s), r(this, "unselect", s);
						}
						isValid() {
							return n(this.y) && this.y >= 0;
						}
						setVisible(t, e = !0) {
							t !== this.visible &&
								this.update({ visible: t ?? !this.visible }, e, void 0, !1);
						}
						slice(t, e, i) {
							let r = this.series;
							s(i, r.chart),
								(e = h(e, !0)),
								(this.sliced =
									this.options.sliced =
									t =
										o(t) ? t : !this.sliced),
								(r.options.data[r.data.indexOf(this)] = this.options),
								this.graphic && this.graphic.animate(this.getTranslate());
						}
					}
					return (
						a(d.prototype, {
							connectorShapes: {
								fixedOffset: function (t, e, i) {
									let s = e.breakAt,
										r = e.touchingSliceAt,
										o = i.softConnector
											? [
													"C",
													t.x + ("left" === t.alignment ? -5 : 5),
													t.y,
													2 * s.x - r.x,
													2 * s.y - r.y,
													s.x,
													s.y,
											  ]
											: ["L", s.x, s.y];
									return [["M", t.x, t.y], o, ["L", r.x, r.y]];
								},
								straight: function (t, e) {
									let i = e.touchingSliceAt;
									return [
										["M", t.x, t.y],
										["L", i.x, i.y],
									];
								},
								crookedLine: function (t, e, i) {
									let { breakAt: s, touchingSliceAt: r } = e,
										{ series: o } = this,
										[a, n, h] = o.center,
										d = h / 2,
										{ plotLeft: c, plotWidth: p } = o.chart,
										u = "left" === t.alignment,
										{ x: g, y: f } = t,
										m = s.x;
									if (i.crookDistance) {
										let t = l(i.crookDistance, 1);
										m = u ? a + d + (p + c - a - d) * (1 - t) : c + (a - d) * t;
									} else
										m = a + (n - f) * Math.tan((this.angle || 0) - Math.PI / 2);
									let x = [["M", g, f]];
									return (
										(u ? m <= g && m >= s.x : m >= g && m <= s.x) &&
											x.push(["L", m, f]),
										x.push(["L", s.x, s.y], ["L", r.x, r.y]),
										x
									);
								},
							},
						}),
						d
					);
				}
			),
			i(e, "Series/Pie/PieSeriesDefaults.js", [], function () {
				return {
					borderRadius: 3,
					center: [null, null],
					clip: !1,
					colorByPoint: !0,
					dataLabels: {
						connectorPadding: 5,
						connectorShape: "crookedLine",
						crookDistance: void 0,
						distance: 30,
						enabled: !0,
						formatter: function () {
							return this.point.isNull ? void 0 : this.point.name;
						},
						softConnector: !0,
						x: 0,
					},
					fillColor: void 0,
					ignoreHiddenPoint: !0,
					inactiveOtherPoints: !0,
					legendType: "point",
					marker: null,
					size: null,
					showInLegend: !1,
					slicedOffset: 10,
					stickyTracking: !1,
					tooltip: { followPointer: !0 },
					borderColor: "#ffffff",
					borderWidth: 1,
					lineWidth: void 0,
					states: { hover: { brightness: 0.1 } },
				};
			}),
			i(
				e,
				"Series/Pie/PieSeries.js",
				[
					e["Series/CenteredUtilities.js"],
					e["Series/Column/ColumnSeries.js"],
					e["Core/Globals.js"],
					e["Series/Pie/PiePoint.js"],
					e["Series/Pie/PieSeriesDefaults.js"],
					e["Core/Series/Series.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Renderer/SVG/Symbols.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r, o, a, n, h) {
					let { getStartAndEndRadians: l } = t,
						{ noop: d } = i,
						{ clamp: c, extend: p, fireEvent: u, merge: g, pick: f } = h;
					class m extends o {
						animate(t) {
							let e = this,
								i = e.points,
								s = e.startAngleRad;
							t ||
								i.forEach(function (t) {
									let i = t.graphic,
										r = t.shapeArgs;
									i &&
										r &&
										(i.attr({
											r: f(t.startR, e.center && e.center[3] / 2),
											start: s,
											end: s,
										}),
										i.animate(
											{ r: r.r, start: r.start, end: r.end },
											e.options.animation
										));
								});
						}
						drawEmpty() {
							let t, e;
							let i = this.startAngleRad,
								s = this.endAngleRad,
								r = this.options;
							0 === this.total && this.center
								? ((t = this.center[0]),
								  (e = this.center[1]),
								  this.graph ||
										(this.graph = this.chart.renderer
											.arc(t, e, this.center[1] / 2, 0, i, s)
											.addClass("highcharts-empty-series")
											.add(this.group)),
								  this.graph.attr({
										d: n.arc(t, e, this.center[2] / 2, 0, {
											start: i,
											end: s,
											innerR: this.center[3] / 2,
										}),
								  }),
								  this.chart.styledMode ||
										this.graph.attr({
											"stroke-width": r.borderWidth,
											fill: r.fillColor || "none",
											stroke: r.color || "#cccccc",
										}))
								: this.graph && (this.graph = this.graph.destroy());
						}
						drawPoints() {
							let t = this.chart.renderer;
							this.points.forEach(function (e) {
								e.graphic &&
									e.hasNewShapeType() &&
									(e.graphic = e.graphic.destroy()),
									e.graphic ||
										((e.graphic = t[e.shapeType](e.shapeArgs).add(
											e.series.group
										)),
										(e.delayedRendering = !0));
							});
						}
						generatePoints() {
							super.generatePoints(), this.updateTotals();
						}
						getX(t, e, i, s) {
							let r = this.center,
								o = this.radii ? this.radii[i.index] || 0 : r[2] / 2,
								a = s.dataLabelPosition,
								n = a?.distance || 0,
								h = Math.asin(c((t - r[1]) / (o + n), -1, 1));
							return (
								r[0] +
								Math.cos(h) * (o + n) * (e ? -1 : 1) +
								(n > 0 ? (e ? -1 : 1) * (s.padding || 0) : 0)
							);
						}
						hasData() {
							return !!this.processedXData.length;
						}
						redrawPoints() {
							let t, e, i, s;
							let r = this,
								o = r.chart;
							this.drawEmpty(),
								r.group && !o.styledMode && r.group.shadow(r.options.shadow),
								r.points.forEach(function (a) {
									let n = {};
									(e = a.graphic),
										!a.isNull && e
											? ((s = a.shapeArgs),
											  (t = a.getTranslate()),
											  o.styledMode ||
													(i = r.pointAttribs(a, a.selected && "select")),
											  a.delayedRendering
													? (e.setRadialReference(r.center).attr(s).attr(t),
													  o.styledMode ||
															e.attr(i).attr({ "stroke-linejoin": "round" }),
													  (a.delayedRendering = !1))
													: (e.setRadialReference(r.center),
													  o.styledMode || g(!0, n, i),
													  g(!0, n, s, t),
													  e.animate(n)),
											  e.attr({
													visibility: a.visible ? "inherit" : "hidden",
											  }),
											  e.addClass(a.getClassName(), !0))
											: e && (a.graphic = e.destroy());
								});
						}
						sortByAngle(t, e) {
							t.sort(function (t, i) {
								return void 0 !== t.angle && (i.angle - t.angle) * e;
							});
						}
						translate(t) {
							u(this, "translate"), this.generatePoints();
							let e = this.options,
								i = e.slicedOffset,
								s = l(e.startAngle, e.endAngle),
								r = (this.startAngleRad = s.start),
								o = (this.endAngleRad = s.end) - r,
								a = this.points,
								n = e.ignoreHiddenPoint,
								h = a.length,
								d,
								c,
								p,
								g,
								f,
								m,
								x,
								y = 0;
							for (
								t || (this.center = t = this.getCenter()), m = 0;
								m < h;
								m++
							) {
								(x = a[m]),
									(d = r + y * o),
									x.isValid() && (!n || x.visible) && (y += x.percentage / 100),
									(c = r + y * o);
								let e = {
									x: t[0],
									y: t[1],
									r: t[2] / 2,
									innerR: t[3] / 2,
									start: Math.round(1e3 * d) / 1e3,
									end: Math.round(1e3 * c) / 1e3,
								};
								(x.shapeType = "arc"),
									(x.shapeArgs = e),
									(p = (c + d) / 2) > 1.5 * Math.PI
										? (p -= 2 * Math.PI)
										: p < -Math.PI / 2 && (p += 2 * Math.PI),
									(x.slicedTranslation = {
										translateX: Math.round(Math.cos(p) * i),
										translateY: Math.round(Math.sin(p) * i),
									}),
									(g = (Math.cos(p) * t[2]) / 2),
									(f = (Math.sin(p) * t[2]) / 2),
									(x.tooltipPos = [t[0] + 0.7 * g, t[1] + 0.7 * f]),
									(x.half = p < -Math.PI / 2 || p > Math.PI / 2 ? 1 : 0),
									(x.angle = p);
							}
							u(this, "afterTranslate");
						}
						updateTotals() {
							let t = this.points,
								e = t.length,
								i = this.options.ignoreHiddenPoint,
								s,
								r,
								o = 0;
							for (s = 0; s < e; s++)
								(r = t[s]).isValid() && (!i || r.visible) && (o += r.y);
							for (s = 0, this.total = o; s < e; s++)
								((r = t[s]).percentage =
									o > 0 && (r.visible || !i) ? (r.y / o) * 100 : 0),
									(r.total = o);
						}
					}
					return (
						(m.defaultOptions = g(o.defaultOptions, r)),
						p(m.prototype, {
							axisTypes: [],
							directTouch: !0,
							drawGraph: void 0,
							drawTracker: e.prototype.drawTracker,
							getCenter: t.getCenter,
							getSymbol: d,
							invertible: !1,
							isCartesian: !1,
							noSharedTooltip: !0,
							pointAttribs: e.prototype.pointAttribs,
							pointClass: s,
							requireSorting: !1,
							searchPoint: d,
							trackerGroups: ["group", "dataLabelsGroup"],
						}),
						a.registerSeriesType("pie", m),
						m
					);
				}
			),
			i(
				e,
				"Series/Pie/PieDataLabel.js",
				[
					e["Core/Series/DataLabel.js"],
					e["Core/Globals.js"],
					e["Core/Renderer/RendererUtilities.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Core/Utilities.js"],
				],
				function (t, e, i, s, r) {
					var o;
					let { composed: a, noop: n } = e,
						{ distribute: h } = i,
						{ series: l } = s,
						{
							arrayMax: d,
							clamp: c,
							defined: p,
							pick: u,
							pushUnique: g,
							relativeLength: f,
						} = r;
					return (
						(function (e) {
							let i = {
								radialDistributionY: function (t, e) {
									return (e.dataLabelPosition?.top || 0) + t.distributeBox.pos;
								},
								radialDistributionX: function (t, e, i, s, r) {
									let o = r.dataLabelPosition;
									return t.getX(
										i < (o?.top || 0) + 2 || i > (o?.bottom || 0) - 2 ? s : i,
										e.half,
										e,
										r
									);
								},
								justify: function (t, e, i, s) {
									return (
										s[0] +
										(t.half ? -1 : 1) *
											(i + (e.dataLabelPosition?.distance || 0))
									);
								},
								alignToPlotEdges: function (t, e, i, s) {
									let r = t.getBBox().width;
									return e ? r + s : i - r - s;
								},
								alignToConnectors: function (t, e, i, s) {
									let r = 0,
										o;
									return (
										t.forEach(function (t) {
											(o = t.dataLabel.getBBox().width) > r && (r = o);
										}),
										e ? r + s : i - r - s
									);
								},
							};
							function s(t, e) {
								let { center: i, options: s } = this,
									r = i[2] / 2,
									o = t.angle || 0,
									a = Math.cos(o),
									n = Math.sin(o),
									h = i[0] + a * r,
									l = i[1] + n * r,
									d = Math.min(
										(s.slicedOffset || 0) + (s.borderWidth || 0),
										e / 5
									);
								return {
									natural: { x: h + a * e, y: l + n * e },
									computed: {},
									alignment: e < 0 ? "center" : t.half ? "right" : "left",
									connectorPosition: {
										breakAt: { x: h + a * d, y: l + n * d },
										touchingSliceAt: { x: h, y: l },
									},
									distance: e,
								};
							}
							function r() {
								let t = this,
									e = t.points,
									i = t.chart,
									s = i.plotWidth,
									r = i.plotHeight,
									o = i.plotLeft,
									a = Math.round(i.chartWidth / 3),
									n = t.center,
									c = n[2] / 2,
									g = n[1],
									m = [[], []],
									x = [0, 0, 0, 0],
									y = t.dataLabelPositioners,
									b,
									v,
									S,
									k = 0;
								t.visible &&
									t.hasDataLabels?.() &&
									(e.forEach((t) => {
										(t.dataLabels || []).forEach((t) => {
											t.shortened &&
												(t
													.attr({ width: "auto" })
													.css({ width: "auto", textOverflow: "clip" }),
												(t.shortened = !1));
										});
									}),
									l.prototype.drawDataLabels.apply(t),
									e.forEach((t) => {
										(t.dataLabels || []).forEach((e, i) => {
											let s = n[2] / 2,
												r = e.options,
												o = f(r?.distance || 0, s);
											0 === i && m[t.half].push(t),
												!p(r?.style?.width) &&
													e.getBBox().width > a &&
													(e.css({ width: Math.round(0.7 * a) + "px" }),
													(e.shortened = !0)),
												(e.dataLabelPosition = this.getDataLabelPosition(t, o)),
												(k = Math.max(k, o));
										});
									}),
									m.forEach((e, a) => {
										let l = e.length,
											d = [],
											f,
											m,
											b = 0,
											C;
										l &&
											(t.sortByAngle(e, a - 0.5),
											k > 0 &&
												((f = Math.max(0, g - c - k)),
												(m = Math.min(g + c + k, i.plotHeight)),
												e.forEach((t) => {
													(t.dataLabels || []).forEach((e) => {
														let s = e.dataLabelPosition;
														s &&
															s.distance > 0 &&
															((s.top = Math.max(0, g - c - s.distance)),
															(s.bottom = Math.min(
																g + c + s.distance,
																i.plotHeight
															)),
															(b = e.getBBox().height || 21),
															(t.distributeBox = {
																target:
																	(e.dataLabelPosition?.natural.y || 0) -
																	s.top +
																	b / 2,
																size: b,
																rank: t.y,
															}),
															d.push(t.distributeBox));
													});
												}),
												h(d, (C = m + b - f), C / 5)),
											e.forEach((i) => {
												(i.dataLabels || []).forEach((h) => {
													let l = h.options || {},
														g = i.distributeBox,
														f = h.dataLabelPosition,
														m = f?.natural.y || 0,
														b = l.connectorPadding || 0,
														k = 0,
														C = m,
														M = "inherit";
													if (f) {
														if (
															(d &&
																p(g) &&
																f.distance > 0 &&
																(void 0 === g.pos
																	? (M = "hidden")
																	: ((S = g.size),
																	  (C = y.radialDistributionY(i, h)))),
															l.justify)
														)
															k = y.justify(i, h, c, n);
														else
															switch (l.alignTo) {
																case "connectors":
																	k = y.alignToConnectors(e, a, s, o);
																	break;
																case "plotEdges":
																	k = y.alignToPlotEdges(h, a, s, o);
																	break;
																default:
																	k = y.radialDistributionX(t, i, C, m, h);
															}
														if (
															((f.attribs = {
																visibility: M,
																align: f.alignment,
															}),
															(f.posAttribs = {
																x:
																	k +
																	(l.x || 0) +
																	({ left: b, right: -b }[f.alignment] || 0),
																y: C + (l.y || 0) - h.getBBox().height / 2,
															}),
															(f.computed.x = k),
															(f.computed.y = C),
															u(l.crop, !0))
														) {
															let t;
															k - (v = h.getBBox().width) < b && 1 === a
																? ((t = Math.round(v - k + b)),
																  (x[3] = Math.max(t, x[3])))
																: k + v > s - b &&
																  0 === a &&
																  ((t = Math.round(k + v - s + b)),
																  (x[1] = Math.max(t, x[1]))),
																C - S / 2 < 0
																	? (x[0] = Math.max(
																			Math.round(-C + S / 2),
																			x[0]
																	  ))
																	: C + S / 2 > r &&
																	  (x[2] = Math.max(
																			Math.round(C + S / 2 - r),
																			x[2]
																	  )),
																(f.sideOverflow = t);
														}
													}
												});
											}));
									}),
									(0 === d(x) || this.verifyDataLabelOverflow(x)) &&
										(this.placeDataLabels(),
										this.points.forEach((e) => {
											(e.dataLabels || []).forEach((s) => {
												let { connectorColor: r, connectorWidth: o = 1 } =
														s.options || {},
													a = s.dataLabelPosition;
												if (o) {
													let n;
													(b = s.connector),
														a && a.distance > 0
															? ((n = !b),
															  b ||
																	(s.connector = b =
																		i.renderer
																			.path()
																			.addClass(
																				"highcharts-data-label-connector  highcharts-color-" +
																					e.colorIndex +
																					(e.className ? " " + e.className : "")
																			)
																			.add(t.dataLabelsGroup)),
															  i.styledMode ||
																	b.attr({
																		"stroke-width": o,
																		stroke: r || e.color || "#666666",
																	}),
															  b[n ? "attr" : "animate"]({
																	d: e.getConnectorPath(s),
															  }),
															  b.attr({ visibility: a.attribs?.visibility }))
															: b && (s.connector = b.destroy());
												}
											});
										})));
							}
							function o() {
								this.points.forEach((t) => {
									(t.dataLabels || []).forEach((t) => {
										let e = t.dataLabelPosition;
										e
											? (e.sideOverflow &&
													(t.css({
														width:
															Math.max(t.getBBox().width - e.sideOverflow, 0) +
															"px",
														textOverflow:
															(t.options?.style || {}).textOverflow ||
															"ellipsis",
													}),
													(t.shortened = !0)),
											  t.attr(e.attribs),
											  t[t.moved ? "animate" : "attr"](e.posAttribs),
											  (t.moved = !0))
											: t && t.attr({ y: -9999 });
									}),
										delete t.distributeBox;
								}, this);
							}
							function m(t) {
								let e = this.center,
									i = this.options,
									s = i.center,
									r = i.minSize || 80,
									o = r,
									a = null !== i.size;
								return (
									!a &&
										(null !== s[0]
											? (o = Math.max(e[2] - Math.max(t[1], t[3]), r))
											: ((o = Math.max(e[2] - t[1] - t[3], r)),
											  (e[0] += (t[3] - t[1]) / 2)),
										null !== s[1]
											? (o = c(o, r, e[2] - Math.max(t[0], t[2])))
											: ((o = c(o, r, e[2] - t[0] - t[2])),
											  (e[1] += (t[0] - t[2]) / 2)),
										o < e[2]
											? ((e[2] = o),
											  (e[3] = Math.min(
													i.thickness
														? Math.max(0, o - 2 * i.thickness)
														: Math.max(0, f(i.innerSize || 0, o)),
													o
											  )),
											  this.translate(e),
											  this.drawDataLabels && this.drawDataLabels())
											: (a = !0)),
									a
								);
							}
							e.compose = function (e) {
								if ((t.compose(l), g(a, "PieDataLabel"))) {
									let t = e.prototype;
									(t.dataLabelPositioners = i),
										(t.alignDataLabel = n),
										(t.drawDataLabels = r),
										(t.getDataLabelPosition = s),
										(t.placeDataLabels = o),
										(t.verifyDataLabelOverflow = m);
								}
							};
						})(o || (o = {})),
						o
					);
				}
			),
			i(
				e,
				"Extensions/OverlappingDataLabels.js",
				[e["Core/Utilities.js"]],
				function (t) {
					let { addEvent: e, fireEvent: i, objectEach: s, pick: r } = t;
					function o(t) {
						let e = t.length,
							s = (t, e) =>
								!(
									e.x >= t.x + t.width ||
									e.x + e.width <= t.x ||
									e.y >= t.y + t.height ||
									e.y + e.height <= t.y
								),
							r,
							o,
							n,
							h,
							l,
							d = !1;
						for (let i = 0; i < e; i++)
							(r = t[i]) &&
								((r.oldOpacity = r.opacity),
								(r.newOpacity = 1),
								(r.absoluteBox = (function (t) {
									if (t && (!t.alignAttr || t.placed)) {
										let e = t.box ? 0 : t.padding || 0,
											i = t.alignAttr || { x: t.attr("x"), y: t.attr("y") },
											s = t.getBBox();
										return (
											(t.width = s.width),
											(t.height = s.height),
											{
												x: i.x + (t.parentGroup?.translateX || 0) + e,
												y: i.y + (t.parentGroup?.translateY || 0) + e,
												width: (t.width || 0) - 2 * e,
												height: (t.height || 0) - 2 * e,
											}
										);
									}
								})(r)));
						t.sort((t, e) => (e.labelrank || 0) - (t.labelrank || 0));
						for (let i = 0; i < e; ++i) {
							h = (o = t[i]) && o.absoluteBox;
							for (let r = i + 1; r < e; ++r)
								(l = (n = t[r]) && n.absoluteBox),
									h &&
										l &&
										o !== n &&
										0 !== o.newOpacity &&
										0 !== n.newOpacity &&
										"hidden" !== o.visibility &&
										"hidden" !== n.visibility &&
										s(h, l) &&
										((o.labelrank < n.labelrank ? o : n).newOpacity = 0);
						}
						for (let e of t) a(e, this) && (d = !0);
						d && i(this, "afterHideAllOverlappingLabels");
					}
					function a(t, e) {
						let s,
							r,
							o = !1;
						return (
							t &&
								((r = t.newOpacity),
								t.oldOpacity !== r &&
									(t.hasClass("highcharts-data-label")
										? (t[r ? "removeClass" : "addClass"](
												"highcharts-data-label-hidden"
										  ),
										  (s = function () {
												e.styledMode ||
													t.css({ pointerEvents: r ? "auto" : "none" });
										  }),
										  (o = !0),
										  t[t.isOld ? "animate" : "attr"](
												{ opacity: r },
												void 0,
												s
										  ),
										  i(e, "afterHideOverlappingLabel"))
										: t.attr({ opacity: r })),
								(t.isOld = !0)),
							o
						);
					}
					function n() {
						let t = this,
							e = [];
						for (let i of t.labelCollectors || []) e = e.concat(i());
						for (let i of t.yAxis || [])
							i.stacking &&
								i.options.stackLabels &&
								!i.options.stackLabels.allowOverlap &&
								s(i.stacking.stacks, (t) => {
									s(t, (t) => {
										t.label && e.push(t.label);
									});
								});
						for (let i of t.series || [])
							if (i.visible && i.hasDataLabels?.()) {
								let s = (i) => {
									for (let s of i)
										s.visible &&
											(s.dataLabels || []).forEach((i) => {
												let o = i.options || {};
												(i.labelrank = r(
													o.labelrank,
													s.labelrank,
													s.shapeArgs?.height
												)),
													o.allowOverlap ?? Number(o.distance) > 0
														? ((i.oldOpacity = i.opacity),
														  (i.newOpacity = 1),
														  a(i, t))
														: e.push(i);
											});
								};
								s(i.nodes || []), s(i.points);
							}
						this.hideOverlappingLabels(e);
					}
					return {
						compose: function (t) {
							let i = t.prototype;
							i.hideOverlappingLabels ||
								((i.hideOverlappingLabels = o), e(t, "render", n));
						},
					};
				}
			),
			i(
				e,
				"Extensions/BorderRadius.js",
				[e["Core/Defaults.js"], e["Core/Globals.js"], e["Core/Utilities.js"]],
				function (t, e, i) {
					let { defaultOptions: s } = t,
						{ noop: r } = e,
						{
							addEvent: o,
							extend: a,
							isObject: n,
							merge: h,
							relativeLength: l,
						} = i,
						d = { radius: 0, scope: "stack", where: void 0 },
						c = r,
						p = r;
					function u(t, e, i, s, r = {}) {
						let o = c(t, e, i, s, r),
							{ innerR: a = 0, r: n = i, start: h = 0, end: d = 0 } = r;
						if (r.open || !r.borderRadius) return o;
						let p = d - h,
							u = Math.sin(p / 2),
							g = Math.max(
								Math.min(
									l(r.borderRadius || 0, n - a),
									(n - a) / 2,
									(n * u) / (1 + u)
								),
								0
							),
							f = Math.min(g, (p / Math.PI) * 2 * a),
							m = o.length - 1;
						for (; m--; )
							!(function (t, e, i) {
								let s, r, o;
								let a = t[e],
									n = t[e + 1];
								if (
									("Z" === n[0] && (n = t[0]),
									("M" === a[0] || "L" === a[0]) && "A" === n[0]
										? ((s = a), (r = n), (o = !0))
										: "A" === a[0] &&
										  ("M" === n[0] || "L" === n[0]) &&
										  ((s = n), (r = a)),
									s && r && r.params)
								) {
									let a = r[1],
										n = r[5],
										h = r.params,
										{ start: l, end: d, cx: c, cy: p } = h,
										u = n ? a - i : a + i,
										g = u ? Math.asin(i / u) : 0,
										f = n ? g : -g,
										m = Math.cos(g) * u;
									o
										? ((h.start = l + f),
										  (s[1] = c + m * Math.cos(l)),
										  (s[2] = p + m * Math.sin(l)),
										  t.splice(e + 1, 0, [
												"A",
												i,
												i,
												0,
												0,
												1,
												c + a * Math.cos(h.start),
												p + a * Math.sin(h.start),
										  ]))
										: ((h.end = d - f),
										  (r[6] = c + a * Math.cos(h.end)),
										  (r[7] = p + a * Math.sin(h.end)),
										  t.splice(e + 1, 0, [
												"A",
												i,
												i,
												0,
												0,
												1,
												c + m * Math.cos(d),
												p + m * Math.sin(d),
										  ])),
										(r[4] = Math.abs(h.end - h.start) < Math.PI ? 0 : 1);
								}
							})(o, m, m > 1 ? f : g);
						return o;
					}
					function g() {
						if (
							this.options.borderRadius &&
							!(this.chart.is3d && this.chart.is3d())
						) {
							let { options: t, yAxis: e } = this,
								i = "percent" === t.stacking,
								r = s.plotOptions?.[this.type]?.borderRadius,
								o = f(t.borderRadius, n(r) ? r : {}),
								h = e.options.reversed;
							for (let s of this.points) {
								let { shapeArgs: r } = s;
								if ("roundedRect" === s.shapeType && r) {
									let { width: n = 0, height: d = 0, y: c = 0 } = r,
										p = c,
										u = d;
									if ("stack" === o.scope && s.stackTotal) {
										let r = e.translate(i ? 100 : s.stackTotal, !1, !0, !1, !0),
											o = e.translate(t.threshold || 0, !1, !0, !1, !0),
											a = this.crispCol(0, Math.min(r, o), 0, Math.abs(r - o));
										(p = a.y), (u = a.height);
									}
									let g = (s.negative ? -1 : 1) * (h ? -1 : 1) == -1,
										f = o.where;
									!f &&
										this.is("waterfall") &&
										Math.abs(
											(s.yBottom || 0) - (this.translatedThreshold || 0)
										) > this.borderWidth &&
										(f = "all"),
										f || (f = "end");
									let m =
										Math.min(
											l(o.radius, n),
											n / 2,
											"all" === f ? d / 2 : 1 / 0
										) || 0;
									"end" === f && (g && (p -= m), (u += m)),
										a(r, { brBoxHeight: u, brBoxY: p, r: m });
								}
							}
						}
					}
					function f(t, e) {
						return n(t) || (t = { radius: t || 0 }), h(d, e, t);
					}
					function m() {
						let t = f(this.options.borderRadius);
						for (let e of this.points) {
							let i = e.shapeArgs;
							i && (i.borderRadius = l(t.radius, (i.r || 0) - (i.innerR || 0)));
						}
					}
					function x(t, e, i, s, r = {}) {
						let o = p(t, e, i, s, r),
							{ r: a = 0, brBoxHeight: n = s, brBoxY: h = e } = r,
							l = e - h,
							d = h + n - (e + s),
							c = l - a > -0.1 ? 0 : a,
							u = d - a > -0.1 ? 0 : a,
							g = Math.max(c && l, 0),
							f = Math.max(u && d, 0),
							m = [t + c, e],
							x = [t + i - c, e],
							y = [t + i, e + c],
							b = [t + i, e + s - u],
							v = [t + i - u, e + s],
							S = [t + u, e + s],
							k = [t, e + s - u],
							C = [t, e + c],
							M = (t, e) => Math.sqrt(Math.pow(t, 2) - Math.pow(e, 2));
						if (g) {
							let t = M(c, c - g);
							(m[0] -= t), (x[0] += t), (y[1] = C[1] = e + c - g);
						}
						if (s < c - g) {
							let r = M(c, c - g - s);
							(y[0] = b[0] = t + i - c + r),
								(v[0] = Math.min(y[0], v[0])),
								(S[0] = Math.max(b[0], S[0])),
								(k[0] = C[0] = t + c - r),
								(y[1] = C[1] = e + s);
						}
						if (f) {
							let t = M(u, u - f);
							(v[0] += t), (S[0] -= t), (b[1] = k[1] = e + s - u + f);
						}
						if (s < u - f) {
							let r = M(u, u - f - s);
							(y[0] = b[0] = t + i - u + r),
								(x[0] = Math.min(y[0], x[0])),
								(m[0] = Math.max(b[0], m[0])),
								(k[0] = C[0] = t + u - r),
								(b[1] = k[1] = e);
						}
						return (
							(o.length = 0),
							o.push(
								["M", ...m],
								["L", ...x],
								["A", c, c, 0, 0, 1, ...y],
								["L", ...b],
								["A", u, u, 0, 0, 1, ...v],
								["L", ...S],
								["A", u, u, 0, 0, 1, ...k],
								["L", ...C],
								["A", c, c, 0, 0, 1, ...m],
								["Z"]
							),
							o
						);
					}
					return {
						compose: function (t, e, i) {
							let s = t.types.pie;
							if (!e.symbolCustomAttribs.includes("borderRadius")) {
								let r = i.prototype.symbols;
								o(t, "afterColumnTranslate", g, { order: 9 }),
									o(s, "afterTranslate", m),
									e.symbolCustomAttribs.push(
										"borderRadius",
										"brBoxHeight",
										"brBoxY"
									),
									(c = r.arc),
									(p = r.roundedRect),
									(r.arc = u),
									(r.roundedRect = x);
							}
						},
						optionsToObject: f,
					};
				}
			),
			i(e, "Core/Responsive.js", [e["Core/Utilities.js"]], function (t) {
				var e;
				let {
					diffObjects: i,
					extend: s,
					find: r,
					merge: o,
					pick: a,
					uniqueKey: n,
				} = t;
				return (
					(function (t) {
						function e(t, e) {
							let i = t.condition;
							(
								i.callback ||
								function () {
									return (
										this.chartWidth <= a(i.maxWidth, Number.MAX_VALUE) &&
										this.chartHeight <= a(i.maxHeight, Number.MAX_VALUE) &&
										this.chartWidth >= a(i.minWidth, 0) &&
										this.chartHeight >= a(i.minHeight, 0)
									);
								}
							).call(this) && e.push(t._id);
						}
						function h(t, e) {
							let s = this.options.responsive,
								a = this.currentResponsive,
								h = [],
								l;
							!e &&
								s &&
								s.rules &&
								s.rules.forEach((t) => {
									void 0 === t._id && (t._id = n()),
										this.matchResponsiveRule(t, h);
								}, this);
							let d = o(
								...h
									.map((t) => r((s || {}).rules || [], (e) => e._id === t))
									.map((t) => t && t.chartOptions)
							);
							(d.isResponsiveOptions = !0), (h = h.toString() || void 0);
							let c = a && a.ruleIds;
							h !== c &&
								(a && this.update(a.undoOptions, t, !0),
								h
									? (((l = i(
											d,
											this.options,
											!0,
											this.collectionsWithUpdate
									  )).isResponsiveOptions = !0),
									  (this.currentResponsive = {
											ruleIds: h,
											mergedOptions: d,
											undoOptions: l,
									  }),
									  this.update(d, t, !0))
									: (this.currentResponsive = void 0));
						}
						t.compose = function (t) {
							let i = t.prototype;
							return (
								i.matchResponsiveRule ||
									s(i, { matchResponsiveRule: e, setResponsive: h }),
								t
							);
						};
					})(e || (e = {})),
					e
				);
			}),
			i(
				e,
				"masters/highcharts.src.js",
				[
					e["Core/Globals.js"],
					e["Core/Utilities.js"],
					e["Core/Defaults.js"],
					e["Core/Animation/Fx.js"],
					e["Core/Animation/AnimationUtilities.js"],
					e["Core/Renderer/HTML/AST.js"],
					e["Core/Templating.js"],
					e["Core/Renderer/RendererRegistry.js"],
					e["Core/Renderer/RendererUtilities.js"],
					e["Core/Renderer/SVG/SVGElement.js"],
					e["Core/Renderer/SVG/SVGRenderer.js"],
					e["Core/Renderer/HTML/HTMLElement.js"],
					e["Core/Axis/Axis.js"],
					e["Core/Axis/DateTimeAxis.js"],
					e["Core/Axis/LogarithmicAxis.js"],
					e["Core/Axis/PlotLineOrBand/PlotLineOrBand.js"],
					e["Core/Axis/Tick.js"],
					e["Core/Tooltip.js"],
					e["Core/Series/Point.js"],
					e["Core/Pointer.js"],
					e["Core/Legend/Legend.js"],
					e["Core/Legend/LegendSymbol.js"],
					e["Core/Chart/Chart.js"],
					e["Extensions/ScrollablePlotArea.js"],
					e["Core/Axis/Stacking/StackingAxis.js"],
					e["Core/Axis/Stacking/StackItem.js"],
					e["Core/Series/Series.js"],
					e["Core/Series/SeriesRegistry.js"],
					e["Series/Column/ColumnDataLabel.js"],
					e["Series/Pie/PieDataLabel.js"],
					e["Core/Series/DataLabel.js"],
					e["Extensions/OverlappingDataLabels.js"],
					e["Extensions/BorderRadius.js"],
					e["Core/Responsive.js"],
					e["Core/Color/Color.js"],
					e["Core/Time.js"],
				],
				function (
					t,
					e,
					i,
					s,
					r,
					o,
					a,
					n,
					h,
					l,
					d,
					c,
					p,
					u,
					g,
					f,
					m,
					x,
					y,
					b,
					v,
					S,
					k,
					C,
					M,
					w,
					T,
					A,
					P,
					L,
					O,
					D,
					E,
					I,
					j,
					B
				) {
					return (
						(t.AST = o),
						(t.Axis = p),
						(t.Chart = k),
						(t.Color = j),
						(t.DataLabel = O),
						(t.Fx = s),
						(t.HTMLElement = c),
						(t.Legend = v),
						(t.LegendSymbol = S),
						(t.OverlappingDataLabels = t.OverlappingDataLabels || D),
						(t.PlotLineOrBand = f),
						(t.Point = y),
						(t.Pointer = b),
						(t.RendererRegistry = n),
						(t.Series = T),
						(t.SeriesRegistry = A),
						(t.StackItem = w),
						(t.SVGElement = l),
						(t.SVGRenderer = d),
						(t.Templating = a),
						(t.Tick = m),
						(t.Time = B),
						(t.Tooltip = x),
						(t.animate = r.animate),
						(t.animObject = r.animObject),
						(t.chart = k.chart),
						(t.color = j.parse),
						(t.dateFormat = a.dateFormat),
						(t.defaultOptions = i.defaultOptions),
						(t.distribute = h.distribute),
						(t.format = a.format),
						(t.getDeferredAnimation = r.getDeferredAnimation),
						(t.getOptions = i.getOptions),
						(t.numberFormat = a.numberFormat),
						(t.seriesType = A.seriesType),
						(t.setAnimation = r.setAnimation),
						(t.setOptions = i.setOptions),
						(t.stop = r.stop),
						(t.time = i.defaultTime),
						(t.timers = s.timers),
						E.compose(t.Series, t.SVGElement, t.SVGRenderer),
						P.compose(t.Series.types.column),
						O.compose(t.Series),
						u.compose(t.Axis),
						c.compose(t.SVGRenderer),
						v.compose(t.Chart),
						g.compose(t.Axis),
						D.compose(t.Chart),
						L.compose(t.Series.types.pie),
						f.compose(t.Axis),
						b.compose(t.Chart),
						I.compose(t.Chart),
						C.compose(t.Axis, t.Chart, t.Series),
						M.compose(t.Axis, t.Chart, t.Series),
						x.compose(t.Pointer),
						e.extend(t, e),
						t
					);
				}
			),
			(e["masters/highcharts.src.js"]._modules = e),
			e["masters/highcharts.src.js"]
		);
	})
);
