import{q as R,I as oe,v as Z,aS as ze,z as p,a1 as We,B as M,aI as ve,T as Be,a4 as fe,s as z,b as x,ap as le,bj as _e,av as ae,w as he,J as $,h,V as se,b3 as Re,aB as me,bk as Me,G as $e,r as Y,aF as G,F as ge,N as F,bl as be,A as He,a3 as Ae,E as Fe,aL as Oe,L as ye,am as Xe,aM as Se,aj as xe,aa as Ye,ak as Ge,M as Le,aA as Q,au as De,a8 as je,ag as Ue,C as qe,t as _,a5 as Ne,bm as Ke}from"./main-Dr3_htUk.js";import{f as Je,a as Qe,s as Ze}from"./forwardRefs-DWGaNmQL.js";import{m as pe,a as et,u as tt}from"./scopeId-yF8hzUmo.js";import{u as nt}from"./ssrBoot-BoH7JcAp.js";import{M as ot}from"./VImg-BxLvZF-2.js";import{b as ie}from"./index-Ckiq5CHF.js";function lt(e){let{selectedElement:o,containerElement:n,isRtl:t,isHorizontal:s}=e;const d=O(s,n),l=we(s,t,n),v=O(s,o),c=Te(s,o),b=v*.4;return l>c?c-b:l+d<c+v?c-d+v+b:l}function at(e){let{selectedElement:o,containerElement:n,isHorizontal:t}=e;const s=O(t,n),d=Te(t,o),l=O(t,o);return d-s/2+l/2}function ue(e,o){const n=e?"scrollWidth":"scrollHeight";return(o==null?void 0:o[n])||0}function st(e,o){const n=e?"clientWidth":"clientHeight";return(o==null?void 0:o[n])||0}function we(e,o,n){if(!n)return 0;const{scrollLeft:t,offsetWidth:s,scrollWidth:d}=n;return e?o?d-s+t:t:n.scrollTop}function O(e,o){const n=e?"offsetWidth":"offsetHeight";return(o==null?void 0:o[n])||0}function Te(e,o){const n=e?"offsetLeft":"offsetTop";return(o==null?void 0:o[n])||0}const it=Symbol.for("vuetify:v-slide-group"),ke=R({centerActive:Boolean,direction:{type:String,default:"horizontal"},symbol:{type:null,default:it},nextIcon:{type:oe,default:"$next"},prevIcon:{type:oe,default:"$prev"},showArrows:{type:[Boolean,String],validator:e=>typeof e=="boolean"||["always","desktop","mobile"].includes(e)},...Z(),...ze({mobile:null}),...p(),...We({selectedClass:"v-slide-group-item--active"})},"VSlideGroup"),re=M()({name:"VSlideGroup",props:ke(),emits:{"update:modelValue":e=>!0},setup(e,o){let{slots:n}=o;const{isRtl:t}=ve(),{displayClasses:s,mobile:d}=Be(e),l=fe(e,e.symbol),v=z(!1),c=z(0),b=z(0),k=z(0),f=x(()=>e.direction==="horizontal"),{resizeRef:u,contentRect:g}=le(),{resizeRef:r,contentRect:w}=le(),P=_e(),W=x(()=>({container:u.el,duration:200,easing:"easeOutQuart"})),V=x(()=>l.selected.value.length?l.items.value.findIndex(a=>a.id===l.selected.value[0]):-1),I=x(()=>l.selected.value.length?l.items.value.findIndex(a=>a.id===l.selected.value[l.selected.value.length-1]):-1);if(ae){let a=-1;he(()=>[l.selected.value,g.value,w.value,f.value],()=>{cancelAnimationFrame(a),a=requestAnimationFrame(()=>{if(g.value&&w.value){const i=f.value?"width":"height";b.value=g.value[i],k.value=w.value[i],v.value=b.value+1<k.value}if(V.value>=0&&r.el){const i=r.el.children[I.value];y(i,e.centerActive)}})})}const m=z(!1);function y(a,i){let S=0;i?S=at({containerElement:u.el,isHorizontal:f.value,selectedElement:a}):S=lt({containerElement:u.el,isHorizontal:f.value,isRtl:t.value,selectedElement:a}),T(S)}function T(a){if(!ae||!u.el)return;const i=O(f.value,u.el),S=we(f.value,t.value,u.el);if(!(ue(f.value,u.el)<=i||Math.abs(a-S)<16)){if(f.value&&t.value&&u.el){const{scrollWidth:X,offsetWidth:J}=u.el;a=X-J-a}f.value?P.horizontal(a,W.value):P(a,W.value)}}function C(a){const{scrollTop:i,scrollLeft:S}=a.target;c.value=f.value?S:i}function L(a){if(m.value=!0,!(!v.value||!r.el)){for(const i of a.composedPath())for(const S of r.el.children)if(S===i){y(S);return}}}function D(a){m.value=!1}let E=!1;function j(a){var i;!E&&!m.value&&!(a.relatedTarget&&((i=r.el)!=null&&i.contains(a.relatedTarget)))&&B(),E=!1}function U(){E=!0}function q(a){if(!r.el)return;function i(S){a.preventDefault(),B(S)}f.value?a.key==="ArrowRight"?i(t.value?"prev":"next"):a.key==="ArrowLeft"&&i(t.value?"next":"prev"):a.key==="ArrowDown"?i("next"):a.key==="ArrowUp"&&i("prev"),a.key==="Home"?i("first"):a.key==="End"&&i("last")}function B(a){var S,A;if(!r.el)return;let i;if(!a)i=Re(r.el)[0];else if(a==="next"){if(i=(S=r.el.querySelector(":focus"))==null?void 0:S.nextElementSibling,!i)return B("first")}else if(a==="prev"){if(i=(A=r.el.querySelector(":focus"))==null?void 0:A.previousElementSibling,!i)return B("last")}else a==="first"?i=r.el.firstElementChild:a==="last"&&(i=r.el.lastElementChild);i&&i.focus({preventScroll:!0})}function H(a){const i=f.value&&t.value?-1:1,S=(a==="prev"?-i:i)*b.value;let A=c.value+S;if(f.value&&t.value&&u.el){const{scrollWidth:X,offsetWidth:J}=u.el;A+=X-J}T(A)}const N=x(()=>({next:l.next,prev:l.prev,select:l.select,isSelected:l.isSelected})),K=x(()=>{switch(e.showArrows){case"always":return!0;case"desktop":return!d.value;case!0:return v.value||Math.abs(c.value)>0;case"mobile":return d.value||v.value||Math.abs(c.value)>0;default:return!d.value&&(v.value||Math.abs(c.value)>0)}}),te=x(()=>Math.abs(c.value)>1),ne=x(()=>{if(!u.value)return!1;const a=ue(f.value,u.el),i=st(f.value,u.el);return a-i-Math.abs(c.value)>1});return $(()=>h(e.tag,{class:["v-slide-group",{"v-slide-group--vertical":!f.value,"v-slide-group--has-affixes":K.value,"v-slide-group--is-overflowing":v.value},s.value,e.class],style:e.style,tabindex:m.value||l.selected.value.length?-1:0,onFocus:j},{default:()=>{var a,i,S;return[K.value&&h("div",{key:"prev",class:["v-slide-group__prev",{"v-slide-group__prev--disabled":!te.value}],onMousedown:U,onClick:()=>te.value&&H("prev")},[((a=n.prev)==null?void 0:a.call(n,N.value))??h(ie,null,{default:()=>[h(se,{icon:t.value?e.nextIcon:e.prevIcon},null)]})]),h("div",{key:"container",ref:u,class:"v-slide-group__container",onScroll:C},[h("div",{ref:r,class:"v-slide-group__content",onFocusin:L,onFocusout:D,onKeydown:q},[(i=n.default)==null?void 0:i.call(n,N.value)])]),K.value&&h("div",{key:"next",class:["v-slide-group__next",{"v-slide-group__next--disabled":!ne.value}],onMousedown:U,onClick:()=>ne.value&&H("next")},[((S=n.next)==null?void 0:S.call(n,N.value))??h(ie,null,{default:()=>[h(se,{icon:t.value?e.prevIcon:e.nextIcon},null)]})])]}})),{selected:l.selected,scrollTo:H,scrollOffset:c,focus:B}}}),ee=Symbol.for("vuetify:v-tabs"),ut=R({fixed:Boolean,sliderColor:String,hideSlider:Boolean,direction:{type:String,default:"horizontal"},...me(Me({selectedClass:"v-tab--selected",variant:"text"}),["active","block","flat","location","position","symbol"])},"VTab"),rt=M()({name:"VTab",props:ut(),setup(e,o){let{slots:n,attrs:t}=o;const{textColorClasses:s,textColorStyles:d}=$e(e,"sliderColor"),l=Y(),v=Y(),c=x(()=>e.direction==="horizontal"),b=x(()=>{var f,u;return((u=(f=l.value)==null?void 0:f.group)==null?void 0:u.isSelected.value)??!1});function k(f){var g,r;let{value:u}=f;if(u){const w=(r=(g=l.value)==null?void 0:g.$el.parentElement)==null?void 0:r.querySelector(".v-tab--selected .v-tab__slider"),P=v.value;if(!w||!P)return;const W=getComputedStyle(w).color,V=w.getBoundingClientRect(),I=P.getBoundingClientRect(),m=c.value?"x":"y",y=c.value?"X":"Y",T=c.value?"right":"bottom",C=c.value?"width":"height",L=V[m],D=I[m],E=L>D?V[T]-I[T]:V[m]-I[m],j=Math.sign(E)>0?c.value?"right":"bottom":Math.sign(E)<0?c.value?"left":"top":"center",q=(Math.abs(E)+(Math.sign(E)<0?V[C]:I[C]))/Math.max(V[C],I[C])||0,B=V[C]/I[C]||0,H=1.5;Qe(P,{backgroundColor:[W,"currentcolor"],transform:[`translate${y}(${E}px) scale${y}(${B})`,`translate${y}(${E/H}px) scale${y}(${(q-1)/H+1})`,"none"],transformOrigin:Array(3).fill(j)},{duration:225,easing:Ze})}}return $(()=>{const f=G.filterProps(e);return h(G,F({symbol:ee,ref:l,class:["v-tab",e.class],style:e.style,tabindex:b.value?0:-1,role:"tab","aria-selected":String(b.value),active:!1},f,t,{block:e.fixed,maxWidth:e.fixed?300:void 0,"onGroup:selected":k}),{...n,default:()=>{var u;return h(ge,null,[((u=n.default)==null?void 0:u.call(n))??e.text,!e.hideSlider&&h("div",{ref:v,class:["v-tab__slider",s.value],style:d.value},null)])}})}),Je({},l)}}),ct=e=>{const{touchstartX:o,touchendX:n,touchstartY:t,touchendY:s}=e,d=.5,l=16;e.offsetX=n-o,e.offsetY=s-t,Math.abs(e.offsetY)<d*Math.abs(e.offsetX)&&(e.left&&n<o-l&&e.left(e),e.right&&n>o+l&&e.right(e)),Math.abs(e.offsetX)<d*Math.abs(e.offsetY)&&(e.up&&s<t-l&&e.up(e),e.down&&s>t+l&&e.down(e))};function dt(e,o){var t;const n=e.changedTouches[0];o.touchstartX=n.clientX,o.touchstartY=n.clientY,(t=o.start)==null||t.call(o,{originalEvent:e,...o})}function vt(e,o){var t;const n=e.changedTouches[0];o.touchendX=n.clientX,o.touchendY=n.clientY,(t=o.end)==null||t.call(o,{originalEvent:e,...o}),ct(o)}function ft(e,o){var t;const n=e.changedTouches[0];o.touchmoveX=n.clientX,o.touchmoveY=n.clientY,(t=o.move)==null||t.call(o,{originalEvent:e,...o})}function ht(){let e=arguments.length>0&&arguments[0]!==void 0?arguments[0]:{};const o={touchstartX:0,touchstartY:0,touchendX:0,touchendY:0,touchmoveX:0,touchmoveY:0,offsetX:0,offsetY:0,left:e.left,right:e.right,up:e.up,down:e.down,start:e.start,move:e.move,end:e.end};return{touchstart:n=>dt(n,o),touchend:n=>vt(n,o),touchmove:n=>ft(n,o)}}function mt(e,o){var v;const n=o.value,t=n!=null&&n.parent?e.parentElement:e,s=(n==null?void 0:n.options)??{passive:!0},d=(v=o.instance)==null?void 0:v.$.uid;if(!t||!d)return;const l=ht(o.value);t._touchHandlers=t._touchHandlers??Object.create(null),t._touchHandlers[d]=l,be(l).forEach(c=>{t.addEventListener(c,l[c],s)})}function gt(e,o){var d,l;const n=(d=o.value)!=null&&d.parent?e.parentElement:e,t=(l=o.instance)==null?void 0:l.$.uid;if(!(n!=null&&n._touchHandlers)||!t)return;const s=n._touchHandlers[t];be(s).forEach(v=>{n.removeEventListener(v,s[v])}),delete n._touchHandlers[t]}const Ce={mounted:mt,unmounted:gt},Ve=Symbol.for("vuetify:v-window"),Ie=Symbol.for("vuetify:v-window-group"),Ee=R({continuous:Boolean,nextIcon:{type:[Boolean,String,Function,Object],default:"$next"},prevIcon:{type:[Boolean,String,Function,Object],default:"$prev"},reverse:Boolean,showArrows:{type:[Boolean,String],validator:e=>typeof e=="boolean"||e==="hover"},touch:{type:[Object,Boolean],default:void 0},direction:{type:String,default:"horizontal"},modelValue:null,disabled:Boolean,selectedClass:{type:String,default:"v-window-item--active"},mandatory:{type:[Boolean,String],default:"force"},...Z(),...p(),...He()},"VWindow"),ce=M()({name:"VWindow",directives:{Touch:Ce},props:Ee(),emits:{"update:modelValue":e=>!0},setup(e,o){let{slots:n}=o;const{themeClasses:t}=Ae(e),{isRtl:s}=ve(),{t:d}=Fe(),l=fe(e,Ie),v=Y(),c=x(()=>s.value?!e.reverse:e.reverse),b=z(!1),k=x(()=>{const m=e.direction==="vertical"?"y":"x",T=(c.value?!b.value:b.value)?"-reverse":"";return`v-window-${m}${T}-transition`}),f=z(0),u=Y(void 0),g=x(()=>l.items.value.findIndex(m=>l.selected.value.includes(m.id)));he(g,(m,y)=>{const T=l.items.value.length,C=T-1;T<=2?b.value=m<y:m===C&&y===0?b.value=!0:m===0&&y===C?b.value=!1:b.value=m<y}),Oe(Ve,{transition:k,isReversed:b,transitionCount:f,transitionHeight:u,rootRef:v});const r=x(()=>e.continuous||g.value!==0),w=x(()=>e.continuous||g.value!==l.items.value.length-1);function P(){r.value&&l.prev()}function W(){w.value&&l.next()}const V=x(()=>{const m=[],y={icon:s.value?e.nextIcon:e.prevIcon,class:`v-window__${c.value?"right":"left"}`,onClick:l.prev,"aria-label":d("$vuetify.carousel.prev")};m.push(r.value?n.prev?n.prev({props:y}):h(G,y,null):h("div",null,null));const T={icon:s.value?e.prevIcon:e.nextIcon,class:`v-window__${c.value?"left":"right"}`,onClick:l.next,"aria-label":d("$vuetify.carousel.next")};return m.push(w.value?n.next?n.next({props:T}):h(G,T,null):h("div",null,null)),m}),I=x(()=>e.touch===!1?e.touch:{...{left:()=>{c.value?P():W()},right:()=>{c.value?W():P()},start:y=>{let{originalEvent:T}=y;T.stopPropagation()}},...e.touch===!0?{}:e.touch});return $(()=>ye(h(e.tag,{ref:v,class:["v-window",{"v-window--show-arrows-on-hover":e.showArrows==="hover"},t.value,e.class],style:e.style},{default:()=>{var m,y;return[h("div",{class:"v-window__container",style:{height:u.value}},[(m=n.default)==null?void 0:m.call(n,{group:l}),e.showArrows!==!1&&h("div",{class:"v-window__controls"},[V.value])]),(y=n.additional)==null?void 0:y.call(n,{group:l})]}}),[[Xe("touch"),I.value]])),{group:l}}}),bt=R({...me(Ee(),["continuous","nextIcon","prevIcon","showArrows","touch","mandatory"])},"VTabsWindow"),yt=M()({name:"VTabsWindow",props:bt(),emits:{"update:modelValue":e=>!0},setup(e,o){let{slots:n}=o;const t=Se(ee,null),s=xe(e,"modelValue"),d=x({get(){var l;return s.value!=null||!t?s.value:(l=t.items.value.find(v=>t.selected.value.includes(v.id)))==null?void 0:l.value},set(l){s.value=l}});return $(()=>{const l=ce.filterProps(e);return h(ce,F({_as:"VTabsWindow"},l,{modelValue:d.value,"onUpdate:modelValue":v=>d.value=v,class:["v-tabs-window",e.class],style:e.style,mandatory:!1,touch:!1}),n)}),{}}}),Pe=R({reverseTransition:{type:[Boolean,String],default:void 0},transition:{type:[Boolean,String],default:void 0},...Z(),...Ye(),...pe()},"VWindowItem"),de=M()({name:"VWindowItem",directives:{Touch:Ce},props:Pe(),emits:{"group:selected":e=>!0},setup(e,o){let{slots:n}=o;const t=Se(Ve),s=Ge(e,Ie),{isBooted:d}=nt();if(!t||!s)throw new Error("[Vuetify] VWindowItem must be used inside VWindow");const l=z(!1),v=x(()=>d.value&&(t.isReversed.value?e.reverseTransition!==!1:e.transition!==!1));function c(){!l.value||!t||(l.value=!1,t.transitionCount.value>0&&(t.transitionCount.value-=1,t.transitionCount.value===0&&(t.transitionHeight.value=void 0)))}function b(){var r;l.value||!t||(l.value=!0,t.transitionCount.value===0&&(t.transitionHeight.value=Q((r=t.rootRef.value)==null?void 0:r.clientHeight)),t.transitionCount.value+=1)}function k(){c()}function f(r){l.value&&De(()=>{!v.value||!l.value||!t||(t.transitionHeight.value=Q(r.clientHeight))})}const u=x(()=>{const r=t.isReversed.value?e.reverseTransition:e.transition;return v.value?{name:typeof r!="string"?t.transition.value:r,onBeforeEnter:b,onAfterEnter:c,onEnterCancelled:k,onBeforeLeave:b,onAfterLeave:c,onLeaveCancelled:k,onEnter:f}:!1}),{hasContent:g}=et(e,s.isSelected);return $(()=>h(ot,{transition:u.value,disabled:!d.value},{default:()=>{var r;return[ye(h("div",{class:["v-window-item",s.selectedClass.value,e.class],style:e.style},[g.value&&((r=n.default)==null?void 0:r.call(n))]),[[Le,s.isSelected.value]])]}})),{groupItem:s}}}),St=R({...Pe()},"VTabsWindowItem"),xt=M()({name:"VTabsWindowItem",props:St(),setup(e,o){let{slots:n}=o;return $(()=>{const t=de.filterProps(e);return h(de,F({_as:"VTabsWindowItem"},t,{class:["v-tabs-window-item",e.class],style:e.style}),n)}),{}}});function wt(e){return e?e.map(o=>Ke(o)?o:{text:o,value:o}):[]}const Tt=R({alignTabs:{type:String,default:"start"},color:String,fixedTabs:Boolean,items:{type:Array,default:()=>[]},stacked:Boolean,bgColor:String,grow:Boolean,height:{type:[Number,String],default:void 0},hideSlider:Boolean,sliderColor:String,...ke({mandatory:"force",selectedClass:"v-tab-item--selected"}),...je(),...p()},"VTabs"),zt=M()({name:"VTabs",props:Tt(),emits:{"update:modelValue":e=>!0},setup(e,o){let{attrs:n,slots:t}=o;const s=xe(e,"modelValue"),d=x(()=>wt(e.items)),{densityClasses:l}=Ue(e),{backgroundColorClasses:v,backgroundColorStyles:c}=qe(_(e,"bgColor")),{scopeId:b}=tt();return Ne({VTab:{color:_(e,"color"),direction:_(e,"direction"),stacked:_(e,"stacked"),fixed:_(e,"fixedTabs"),sliderColor:_(e,"sliderColor"),hideSlider:_(e,"hideSlider")}}),$(()=>{const k=re.filterProps(e),f=!!(t.window||e.items.length>0);return h(ge,null,[h(re,F(k,{modelValue:s.value,"onUpdate:modelValue":u=>s.value=u,class:["v-tabs",`v-tabs--${e.direction}`,`v-tabs--align-tabs-${e.alignTabs}`,{"v-tabs--fixed-tabs":e.fixedTabs,"v-tabs--grow":e.grow,"v-tabs--stacked":e.stacked},l.value,v.value,e.class],style:[{"--v-tabs-height":Q(e.height)},c.value,e.style],role:"tablist",symbol:ee},b,n),{default:()=>{var u;return[((u=t.default)==null?void 0:u.call(t))??d.value.map(g=>{var r;return((r=t.tab)==null?void 0:r.call(t,{item:g}))??h(rt,F(g,{key:g.text,value:g.value}),{default:t[`tab.${g.value}`]?()=>{var w;return(w=t[`tab.${g.value}`])==null?void 0:w.call(t,{item:g})}:void 0})})]}}),f&&h(yt,F({modelValue:s.value,"onUpdate:modelValue":u=>s.value=u,key:"tabs-window"},b),{default:()=>{var u;return[d.value.map(g=>{var r;return((r=t.item)==null?void 0:r.call(t,{item:g}))??h(xt,{value:g.value},{default:()=>{var w;return(w=t[`item.${g.value}`])==null?void 0:w.call(t,{item:g})}})}),(u=t.window)==null?void 0:u.call(t)]}})])}),{}}});export{re as V,zt as a,ce as b,rt as c,de as d,ke as m};
