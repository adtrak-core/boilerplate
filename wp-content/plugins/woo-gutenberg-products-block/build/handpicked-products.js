this.wc=this.wc||{},this.wc.blocks=this.wc.blocks||{},this.wc.blocks["handpicked-products"]=function(t){function e(e){for(var r,u,i=e[0],s=e[1],a=e[2],p=0,d=[];p<i.length;p++)u=i[p],Object.prototype.hasOwnProperty.call(o,u)&&o[u]&&d.push(o[u][0]),o[u]=0;for(r in s)Object.prototype.hasOwnProperty.call(s,r)&&(t[r]=s[r]);for(l&&l(e);d.length;)d.shift()();return c.push.apply(c,a||[]),n()}function n(){for(var t,e=0;e<c.length;e++){for(var n=c[e],r=!0,i=1;i<n.length;i++){var s=n[i];0!==o[s]&&(r=!1)}r&&(c.splice(e--,1),t=u(u.s=n[0]))}return t}var r={},o={25:0},c=[];function u(e){if(r[e])return r[e].exports;var n=r[e]={i:e,l:!1,exports:{}};return t[e].call(n.exports,n,n.exports,u),n.l=!0,n.exports}u.m=t,u.c=r,u.d=function(t,e,n){u.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},u.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},u.t=function(t,e){if(1&e&&(t=u(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(u.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)u.d(n,r,function(e){return t[e]}.bind(null,r));return n},u.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return u.d(e,"a",e),e},u.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},u.p="";var i=window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[],s=i.push.bind(i);i.push=e,i=i.slice();for(var a=0;a<i.length;a++)e(i[a]);var l=s;return c.push([512,0]),n()}({0:function(t,e){!function(){t.exports=this.wp.element}()},1:function(t,e){!function(){t.exports=this.wp.i18n}()},10:function(t,e){!function(){t.exports=this.regeneratorRuntime}()},102:function(t,e){},103:function(t,e){!function(){t.exports=this.wp.coreData}()},107:function(t,e,n){"use strict";var r=n(0),o=n(1),c=n(3);n(2);e.a=function(t){var e=t.value,n=t.setAttributes;return Object(r.createElement)(c.SelectControl,{label:Object(o.__)("Order products by","woo-gutenberg-products-block"),value:e,options:[{label:Object(o.__)("Newness - newest first","woo-gutenberg-products-block"),value:"date"},{label:Object(o.__)("Price - low to high","woo-gutenberg-products-block"),value:"price_asc"},{label:Object(o.__)("Price - high to low","woo-gutenberg-products-block"),value:"price_desc"},{label:Object(o.__)("Rating - highest first","woo-gutenberg-products-block"),value:"rating"},{label:Object(o.__)("Sales - most first","woo-gutenberg-products-block"),value:"popularity"},{label:Object(o.__)("Title - alphabetical","woo-gutenberg-products-block"),value:"title"},{label:Object(o.__)("Menu Order","woo-gutenberg-products-block"),value:"menu_order"}],onChange:function(t){return n({orderby:t})}})}},14:function(t,e,n){"use strict";n.d(e,"m",(function(){return c})),n.d(e,"k",(function(){return u})),n.d(e,"l",(function(){return i})),n.d(e,"h",(function(){return a})),n.d(e,"c",(function(){return l})),n.d(e,"d",(function(){return p})),n.d(e,"g",(function(){return d})),n.d(e,"f",(function(){return b})),n.d(e,"j",(function(){return f})),n.d(e,"i",(function(){return g})),n.d(e,"a",(function(){return h})),n.d(e,"b",(function(){return O})),n.d(e,"e",(function(){return j})),n.d(e,"p",(function(){return w})),n.d(e,"q",(function(){return v})),n.d(e,"n",(function(){return y})),n.d(e,"o",(function(){return _}));var r,o=n(5),c=Object(o.getSetting)("wcBlocksConfig",{buildPhase:1,pluginUrl:"",productCount:0,restApiRoutes:{},wordCountType:"words"}),u=c.pluginUrl+"assets/",i=c.pluginUrl+"build/",s=c.buildPhase,a=null===(r=o.STORE_PAGES.shop)||void 0===r?void 0:r.permalink,l=o.STORE_PAGES.checkout.id,p=o.STORE_PAGES.checkout.permalink,d=o.STORE_PAGES.privacy.permalink,b=o.STORE_PAGES.privacy.title,f=o.STORE_PAGES.terms.permalink,g=o.STORE_PAGES.terms.title,h=o.STORE_PAGES.cart.id,O=o.STORE_PAGES.cart.permalink,j=o.STORE_PAGES.myaccount.permalink?o.STORE_PAGES.myaccount.permalink:Object(o.getSetting)("wpLoginUrl","/wp-login.php"),m=n(25),w=function(t,e){if(s>2)return Object(m.registerBlockType)(t,e)},v=function(t,e){if(s>1)return Object(m.registerBlockType)(t,e)},y=function(){return s>2},_=function(){return s>1}},159:function(t,e,n){"use strict";n.d(e,"a",(function(){return c}));var r=n(0),o=n(14),c=Object(r.createElement)("img",{src:o.k+"img/grid.svg",alt:"Grid Preview",width:"230",height:"250",style:{width:"100%"}})},19:function(t,e){!function(){t.exports=this.wp.apiFetch}()},21:function(t,e){!function(){t.exports=this.wp.url}()},22:function(t,e){!function(){t.exports=this.wp.compose}()},23:function(t,e){!function(){t.exports=this.wp.data}()},24:function(t,e){!function(){t.exports=this.wp.blockEditor}()},25:function(t,e){!function(){t.exports=this.wp.blocks}()},255:function(t,e,n){"use strict";var r=n(11),o=n.n(r),c=n(31),u=n.n(c),i=n(15),s=n.n(i),a=n(16),l=n.n(a),p=n(12),d=n.n(p),b=n(17),f=n.n(b),g=n(18),h=n.n(g),O=n(9),j=n.n(O),m=n(0),w=n(10),v=n.n(w),y=n(6),_=n(22),k=(n(2),n(14)),E=n(39),P=n(41);function S(t){var e=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(t){return!1}}();return function(){var n,r=j()(t);if(e){var o=j()(this).constructor;n=Reflect.construct(r,arguments,o)}else n=r.apply(this,arguments);return h()(this,n)}}var C=Object(_.createHigherOrderComponent)((function(t){var e=function(e){f()(c,e);var n,r=S(c);function c(){var t;return s()(this,c),(t=r.apply(this,arguments)).state={list:[],loading:!0},t.setError=t.setError.bind(d()(t)),t.debouncedOnSearch=Object(y.debounce)(t.onSearch.bind(d()(t)),400),t}return l()(c,[{key:"componentDidMount",value:function(){var t=this,e=this.props.selected;Object(E.h)({selected:e}).then((function(e){t.setState({list:e,loading:!1})})).catch(this.setError)}},{key:"componentWillUnmount",value:function(){this.debouncedOnSearch.cancel()}},{key:"onSearch",value:function(t){var e=this,n=this.props.selected;Object(E.h)({selected:n,search:t}).then((function(t){e.setState({list:t,loading:!1})})).catch(this.setError)}},{key:"setError",value:(n=u()(v.a.mark((function t(e){var n;return v.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,Object(P.a)(e);case 2:n=t.sent,this.setState({list:[],loading:!1,error:n});case 4:case"end":return t.stop()}}),t,this)}))),function(t){return n.apply(this,arguments)})},{key:"render",value:function(){var e=this,n=this.state,r=n.error,c=n.list,u=n.loading;return Object(m.createElement)(t,o()({},this.props,{error:r,products:c,isLoading:u,onSearch:k.m.productCount>100?function(t){e.setState({loading:!0}),e.debouncedOnSearch(t)}:null}))}}]),c}(m.Component);return e.defaultProps={selected:[]},e}),"withSearchedProducts");e.a=C},29:function(t,e){!function(){t.exports=this.wp.htmlEntities}()},3:function(t,e){!function(){t.exports=this.wp.components}()},30:function(t,e){!function(){t.exports=this.moment}()},33:function(t,e){!function(){t.exports=this.wp.primitives}()},34:function(t,e){!function(){t.exports=this.wp.dataControls}()},39:function(t,e,n){"use strict";n.d(e,"h",(function(){return b})),n.d(e,"e",(function(){return f})),n.d(e,"b",(function(){return g})),n.d(e,"i",(function(){return h})),n.d(e,"f",(function(){return O})),n.d(e,"c",(function(){return j})),n.d(e,"d",(function(){return m})),n.d(e,"g",(function(){return w})),n.d(e,"a",(function(){return v}));var r=n(4),o=n.n(r),c=n(21),u=n(19),i=n.n(u),s=n(6),a=n(5),l=n(14);function p(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,r)}return n}function d(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?p(Object(n),!0).forEach((function(e){o()(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):p(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}var b=function(t){var e=t.selected,n=void 0===e?[]:e,r=t.search,o=void 0===r?"":r,u=t.queryArgs,a=function(t){var e=t.selected,n=void 0===e?[]:e,r=t.search,o=void 0===r?"":r,u=t.queryArgs,i=void 0===u?[]:u,s=l.m.productCount>100,a={per_page:s?100:0,catalog_visibility:"any",search:o,orderby:"title",order:"asc"},p=[Object(c.addQueryArgs)("/wc/store/products",d(d({},a),i))];return s&&n.length&&p.push(Object(c.addQueryArgs)("/wc/store/products",{catalog_visibility:"any",include:n})),p}({selected:n,search:o,queryArgs:void 0===u?[]:u});return Promise.all(a.map((function(t){return i()({path:t})}))).then((function(t){return Object(s.uniqBy)(Object(s.flatten)(t),"id").map((function(t){return d(d({},t),{},{parent:0})}))})).catch((function(t){throw t}))},f=function(t){return i()({path:"/wc/store/products/".concat(t)})},g=function(){return i()({path:"wc/store/products/attributes"})},h=function(t){return i()({path:"wc/store/products/attributes/".concat(t,"/terms")})},O=function(t){var e=t.selected,n=function(t){var e=t.selected,n=void 0===e?[]:e,r=t.search,o=Object(a.getSetting)("limitTags",!1),u=[Object(c.addQueryArgs)("wc/store/products/tags",{per_page:o?100:0,orderby:o?"count":"name",order:o?"desc":"asc",search:r})];return o&&n.length&&u.push(Object(c.addQueryArgs)("wc/store/products/tags",{include:n})),u}({selected:void 0===e?[]:e,search:t.search});return Promise.all(n.map((function(t){return i()({path:t})}))).then((function(t){return Object(s.uniqBy)(Object(s.flatten)(t),"id")}))},j=function(t){return i()({path:Object(c.addQueryArgs)("wc/store/products/categories",d({per_page:0},t))})},m=function(t){return i()({path:"wc/store/products/categories/".concat(t)})},w=function(t){return i()({path:Object(c.addQueryArgs)("wc/store/products",{per_page:0,type:"variation",parent:t})})},v=function(t,e){if(!t.title.raw)return t.slug;var n=1===e.filter((function(e){return e.title.raw===t.title.raw})).length;return t.title.raw+(n?"":" - ".concat(t.slug))}},41:function(t,e,n){"use strict";n.d(e,"a",(function(){return s})),n.d(e,"b",(function(){return a}));var r=n(31),o=n.n(r),c=n(10),u=n.n(c),i=n(1),s=function(){var t=o()(u.a.mark((function t(e){var n;return u.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if("function"!=typeof e.json){t.next=11;break}return t.prev=1,t.next=4,e.json();case 4:return n=t.sent,t.abrupt("return",{message:n.message,type:n.type||"api"});case 8:return t.prev=8,t.t0=t.catch(1),t.abrupt("return",{message:t.t0.message,type:"general"});case 11:return t.abrupt("return",{message:e.message,type:e.type||"general"});case 12:case"end":return t.stop()}}),t,null,[[1,8]])})));return function(e){return t.apply(this,arguments)}}(),a=function(t){if(t.data&&"rest_invalid_param"===t.code){var e=Object.values(t.data.params);if(e[0])return e[0]}return(null==t?void 0:t.message)||Object(i.__)("Something went wrong. Please contact us to get assistance.","woo-gutenberg-products-block")}},45:function(t,e){!function(){t.exports=this.wp.escapeHtml}()},46:function(t,e,n){"use strict";var r=n(0),o=n(1),c=(n(2),n(45));e.a=function(t){var e,n,u,i=t.error;return Object(r.createElement)("div",{className:"wc-block-error-message"},(n=(e=i).message,u=e.type,n?"general"===u?Object(r.createElement)("span",null,Object(o.__)("The following error was returned","woo-gutenberg-products-block"),Object(r.createElement)("br",null),Object(r.createElement)("code",null,Object(c.escapeHTML)(n))):"api"===u?Object(r.createElement)("span",null,Object(o.__)("The following error was returned from the API","woo-gutenberg-products-block"),Object(r.createElement)("br",null),Object(r.createElement)("code",null,Object(c.escapeHTML)(n))):n:Object(o.__)("An unknown error occurred which prevented the block from being updated.","woo-gutenberg-products-block")))}},48:function(t,e){!function(){t.exports=this.wp.keycodes}()},5:function(t,e){!function(){t.exports=this.wc.wcSettings}()},51:function(t,e){!function(){t.exports=this.wp.hooks}()},512:function(t,e,n){t.exports=n(778)},513:function(t,e){},58:function(t,e,n){"use strict";var r=n(4),o=n.n(r),c=n(20),u=n.n(c),i=n(0),s=["srcElement","size"];function a(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,r)}return n}e.a=function(t){var e=t.srcElement,n=t.size,r=void 0===n?24:n,c=u()(t,s);return Object(i.isValidElement)(e)?Object(i.cloneElement)(e,function(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?a(Object(n),!0).forEach((function(e){o()(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):a(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}({width:r,height:r},c)):null}},6:function(t,e){!function(){t.exports=this.lodash}()},61:function(t,e){!function(){t.exports=this.wp.deprecated}()},68:function(t,e){!function(){t.exports=this.wp.serverSideRender}()},7:function(t,e){!function(){t.exports=this.React}()},72:function(t,e){!function(){t.exports=this.wp.dom}()},73:function(t,e){!function(){t.exports=this.ReactDOM}()},77:function(t,e){!function(){t.exports=this.wp.viewport}()},778:function(t,e,n){"use strict";n.r(e);var r=n(0),o=n(1),c=n(25),u=n(5),i=n(58),s=n(33),a=Object(r.createElement)(s.SVG,{xmlns:"http://www.w3.org/2000/SVG",viewBox:"0 0 24 24"},Object(r.createElement)("path",{fill:"none",d:"M0 0h24v24H0V0z"}),Object(r.createElement)("path",{d:"M16.66 4.52l2.83 2.83-2.83 2.83-2.83-2.83 2.83-2.83M9 5v4H5V5h4m10 10v4h-4v-4h4M9 15v4H5v-4h4m7.66-13.31L11 7.34 16.66 13l5.66-5.66-5.66-5.65zM11 3H3v8h8V3zm10 10h-8v8h8v-8zm-10 0H3v8h8v-8z"})),l=(n(513),n(15)),p=n.n(l),d=n(16),b=n.n(d),f=n(17),g=n.n(f),h=n(18),O=n.n(h),j=n(9),m=n.n(j),w=n(24),v=n(68),y=n.n(v),_=n(3),k=(n(2),n(81)),E=n(44),P=n(255),S=n(46),C=function(t){var e=t.error,n=t.onChange,c=t.onSearch,u=t.selected,i=t.products,s=t.isLoading,a=t.isCompact,l={clear:Object(o.__)("Clear all products","woo-gutenberg-products-block"),list:Object(o.__)("Products","woo-gutenberg-products-block"),noItems:Object(o.__)("Your store doesn't have any products.","woo-gutenberg-products-block"),search:Object(o.__)("Search for products to display","woo-gutenberg-products-block"),selected:function(t){return Object(o.sprintf)(Object(o._n)("%d product selected","%d products selected",t,"woo-gutenberg-products-block"),t)},updated:Object(o.__)("Product search results updated.","woo-gutenberg-products-block")};return e?Object(r.createElement)(S.a,{error:e}):Object(r.createElement)(E.b,{className:"woocommerce-products",list:i,isCompact:a,isLoading:s,selected:i.filter((function(t){var e=t.id;return u.includes(e)})),onSearch:c,onChange:n,messages:l})};C.defaultProps={selected:[],products:[],isCompact:!1,isLoading:!0};var x=Object(P.a)(C),A=n(107),T=n(159);function R(t){var e=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(t){return!1}}();return function(){var n,r=m()(t);if(e){var o=m()(this).constructor;n=Reflect.construct(r,arguments,o)}else n=r.apply(this,arguments);return O()(this,n)}}var B=function(t){g()(n,t);var e=R(n);function n(){return p()(this,n),e.apply(this,arguments)}return b()(n,[{key:"getInspectorControls",value:function(){var t=this.props,e=t.attributes,n=t.setAttributes,c=e.columns,i=e.contentVisibility,s=e.orderby,a=e.alignButtons;return Object(r.createElement)(w.InspectorControls,{key:"inspector"},Object(r.createElement)(_.PanelBody,{title:Object(o.__)("Layout","woo-gutenberg-products-block"),initialOpen:!0},Object(r.createElement)(_.RangeControl,{label:Object(o.__)("Columns","woo-gutenberg-products-block"),value:c,onChange:function(t){return n({columns:t})},min:Object(u.getSetting)("min_columns",1),max:Object(u.getSetting)("max_columns",6)}),Object(r.createElement)(_.ToggleControl,{label:Object(o.__)("Align Buttons","woo-gutenberg-products-block"),help:a?Object(o.__)("Buttons are aligned vertically.","woo-gutenberg-products-block"):Object(o.__)("Buttons follow content.","woo-gutenberg-products-block"),checked:a,onChange:function(){return n({alignButtons:!a})}})),Object(r.createElement)(_.PanelBody,{title:Object(o.__)("Content","woo-gutenberg-products-block"),initialOpen:!0},Object(r.createElement)(k.a,{settings:i,onChange:function(t){return n({contentVisibility:t})}})),Object(r.createElement)(_.PanelBody,{title:Object(o.__)("Order By","woo-gutenberg-products-block"),initialOpen:!1},Object(r.createElement)(A.a,{setAttributes:n,value:s})),Object(r.createElement)(_.PanelBody,{title:Object(o.__)("Products","woo-gutenberg-products-block"),initialOpen:!1},Object(r.createElement)(x,{selected:e.products,onChange:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],e=t.map((function(t){return t.id}));n({products:e})},isCompact:!0})))}},{key:"renderEditMode",value:function(){var t=this.props,e=t.attributes,n=t.debouncedSpeak,c=t.setAttributes;return Object(r.createElement)(_.Placeholder,{icon:Object(r.createElement)(i.a,{srcElement:a}),label:Object(o.__)("Hand-picked Products","woo-gutenberg-products-block"),className:"wc-block-products-grid wc-block-handpicked-products"},Object(o.__)("Display a selection of hand-picked products in a grid.","woo-gutenberg-products-block"),Object(r.createElement)("div",{className:"wc-block-handpicked-products__selection"},Object(r.createElement)(x,{selected:e.products,onChange:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],e=t.map((function(t){return t.id}));c({products:e})}}),Object(r.createElement)(_.Button,{isPrimary:!0,onClick:function(){c({editMode:!1}),n(Object(o.__)("Showing Hand-picked Products block preview.","woo-gutenberg-products-block"))}},Object(o.__)("Done","woo-gutenberg-products-block"))))}},{key:"render",value:function(){var t=this.props,e=t.attributes,n=t.name,c=t.setAttributes,u=e.editMode;return e.isPreview?T.a:Object(r.createElement)(r.Fragment,null,Object(r.createElement)(w.BlockControls,null,Object(r.createElement)(_.ToolbarGroup,{controls:[{icon:"edit",title:Object(o.__)("Edit"),onClick:function(){return c({editMode:!u})},isActive:u}]})),this.getInspectorControls(),u?this.renderEditMode():Object(r.createElement)(_.Disabled,null,Object(r.createElement)(y.a,{block:n,attributes:e})))}}]),n}(r.Component),M=Object(_.withSpokenMessages)(B);Object(c.registerBlockType)("woocommerce/handpicked-products",{title:Object(o.__)("Hand-picked Products","woo-gutenberg-products-block"),icon:{src:Object(r.createElement)(i.a,{srcElement:a}),foreground:"#96588a"},category:"woocommerce",keywords:[Object(o.__)("Handpicked Products","woo-gutenberg-products-block"),Object(o.__)("WooCommerce","woo-gutenberg-products-block")],description:Object(o.__)("Display a selection of hand-picked products in a grid.","woo-gutenberg-products-block"),supports:{align:["wide","full"],html:!1},example:{attributes:{isPreview:!0}},attributes:{align:{type:"string"},columns:{type:"number",default:Object(u.getSetting)("default_columns",3)},editMode:{type:"boolean",default:!0},contentVisibility:{type:"object",default:{title:!0,price:!0,rating:!0,button:!0}},orderby:{type:"string",default:"date"},products:{type:"array",default:[]},alignButtons:{type:"boolean",default:!1},isPreview:{type:"boolean",default:!1}},edit:function(t){return Object(r.createElement)(M,t)},save:function(){return null}})},81:function(t,e,n){"use strict";var r=n(4),o=n.n(r),c=n(0),u=n(1),i=(n(2),n(3));function s(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(t);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,r)}return n}function a(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?s(Object(n),!0).forEach((function(e){o()(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):s(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}e.a=function(t){var e=t.onChange,n=t.settings,r=n.button,o=n.price,s=n.rating,l=n.title;return Object(c.createElement)(c.Fragment,null,Object(c.createElement)(i.ToggleControl,{label:Object(u.__)("Product title","woo-gutenberg-products-block"),help:l?Object(u.__)("Product title is visible.","woo-gutenberg-products-block"):Object(u.__)("Product title is hidden.","woo-gutenberg-products-block"),checked:l,onChange:function(){return e(a(a({},n),{},{title:!l}))}}),Object(c.createElement)(i.ToggleControl,{label:Object(u.__)("Product price","woo-gutenberg-products-block"),help:o?Object(u.__)("Product price is visible.","woo-gutenberg-products-block"):Object(u.__)("Product price is hidden.","woo-gutenberg-products-block"),checked:o,onChange:function(){return e(a(a({},n),{},{price:!o}))}}),Object(c.createElement)(i.ToggleControl,{label:Object(u.__)("Product rating","woo-gutenberg-products-block"),help:s?Object(u.__)("Product rating is visible.","woo-gutenberg-products-block"):Object(u.__)("Product rating is hidden.","woo-gutenberg-products-block"),checked:s,onChange:function(){return e(a(a({},n),{},{rating:!s}))}}),Object(c.createElement)(i.ToggleControl,{label:Object(u.__)("Add to Cart button","woo-gutenberg-products-block"),help:r?Object(u.__)("Add to Cart button is visible.","woo-gutenberg-products-block"):Object(u.__)("Add to Cart button is hidden.","woo-gutenberg-products-block"),checked:r,onChange:function(){return e(a(a({},n),{},{button:!r}))}}))}},84:function(t,e){!function(){t.exports=this.wp.date}()}});