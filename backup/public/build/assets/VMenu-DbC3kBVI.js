import{B,h as y,N as _,aX as ve,q as x,aL as N,s as F,aK as R,aY as L,aB as Ve,r as P,ai as X,b as h,R as Ae,aw as lt,t as w,aZ as Pe,a_ as it,I as D,v as K,z as U,J as j,an as Y,L as Ie,M as st,a5 as $,a6 as Le,a7 as xe,av as Oe,a8 as Ee,y as Te,aa as rt,A as Be,a1 as Me,ac as ot,ak as ut,w as De,a2 as Fe,ad as je,ae as ct,af as $e,ax as _e,ag as Ge,D as Ne,a$ as dt,al as vt,am as ft,F as fe,V as me,G as mt,$ as yt,aV as O,aA as ie,C as gt,a4 as ht,b0 as ne,b1 as St,at as bt,b2 as ye,b3 as pt}from"./main-B6lduhDs.js";import{a as wt}from"./index-B8zzSXAV.js";import{u as Ct,V as kt}from"./ssrBoot-BGYu9N7T.js";import{M as Vt}from"./VImg-CuSWVABe.js";import{c as At,V as ge}from"./VAvatar-DCRWcC4u.js";import{m as Pt,V as he,a as Se}from"./VOverlay-D0hc1vwI.js";import{a as q,d as It,s as be,b as Lt,g as xt,n as Ot,f as Et}from"./forwardRefs-DWGaNmQL.js";import{u as Tt}from"./scopeId-HfbfmXgh.js";const Bt=x({target:[Object,Array]},"v-dialog-transition"),Mt=B()({name:"VDialogTransition",props:Bt(),setup(e,i){let{slots:t}=i;const n={onBeforeEnter(l){l.style.pointerEvents="none",l.style.visibility="hidden"},async onEnter(l,a){var g;await new Promise(o=>requestAnimationFrame(o)),await new Promise(o=>requestAnimationFrame(o)),l.style.visibility="";const{x:s,y:r,sx:v,sy:f,speed:m}=we(e.target,l),S=q(l,[{transform:`translate(${s}px, ${r}px) scale(${v}, ${f})`,opacity:0},{}],{duration:225*m,easing:It});(g=pe(l))==null||g.forEach(o=>{q(o,[{opacity:0},{opacity:0,offset:.33},{}],{duration:225*2*m,easing:be})}),S.finished.then(()=>a())},onAfterEnter(l){l.style.removeProperty("pointer-events")},onBeforeLeave(l){l.style.pointerEvents="none"},async onLeave(l,a){var g;await new Promise(o=>requestAnimationFrame(o));const{x:s,y:r,sx:v,sy:f,speed:m}=we(e.target,l);q(l,[{},{transform:`translate(${s}px, ${r}px) scale(${v}, ${f})`,opacity:0}],{duration:125*m,easing:Lt}).finished.then(()=>a()),(g=pe(l))==null||g.forEach(o=>{q(o,[{},{opacity:0,offset:.2},{opacity:0}],{duration:125*2*m,easing:be})})},onAfterLeave(l){l.style.removeProperty("pointer-events")}};return()=>e.target?y(ve,_({name:"dialog-transition"},n,{css:!1}),t):y(ve,{name:"dialog-transition"},t)}});function pe(e){var t;const i=(t=e.querySelector(":scope > .v-card, :scope > .v-sheet, :scope > .v-list"))==null?void 0:t.children;return i&&[...i]}function we(e,i){const t=xt(e),n=Ot(i),[l,a]=getComputedStyle(i).transformOrigin.split(" ").map(b=>parseFloat(b)),[s,r]=getComputedStyle(i).getPropertyValue("--v-overlay-anchor-origin").split(" ");let v=t.left+t.width/2;s==="left"||r==="left"?v-=t.width/2:(s==="right"||r==="right")&&(v+=t.width/2);let f=t.top+t.height/2;s==="top"||r==="top"?f-=t.height/2:(s==="bottom"||r==="bottom")&&(f+=t.height/2);const m=t.width/n.width,S=t.height/n.height,g=Math.max(1,m,S),o=m/g||0,c=S/g||0,u=n.width*n.height/(window.innerWidth*window.innerHeight),d=u>.12?Math.min(1.5,(u-.12)*10+1):1;return{x:v-(l+n.left),y:f-(a+n.top),sx:o,sy:c,speed:d}}const ae=Symbol.for("vuetify:list");function Re(){const e=N(ae,{hasPrepend:F(!1),updateHasPrepend:()=>null}),i={hasPrepend:F(!1),updateHasPrepend:t=>{t&&(i.hasPrepend.value=t)}};return R(ae,i),e}function Ke(){return N(ae,null)}const se=e=>{const i={activate:t=>{let{id:n,value:l,activated:a}=t;return n=L(n),e&&!l&&a.size===1&&a.has(n)||(l?a.add(n):a.delete(n)),a},in:(t,n,l)=>{let a=new Set;if(t!=null)for(const s of Ve(t))a=i.activate({id:s,value:!0,activated:new Set(a),children:n,parents:l});return a},out:t=>Array.from(t)};return i},Ue=e=>{const i=se(e);return{activate:n=>{let{activated:l,id:a,...s}=n;a=L(a);const r=l.has(a)?new Set([a]):new Set;return i.activate({...s,id:a,activated:r})},in:(n,l,a)=>{let s=new Set;if(n!=null){const r=Ve(n);r.length&&(s=i.in(r.slice(0,1),l,a))}return s},out:(n,l,a)=>i.out(n,l,a)}},Dt=e=>{const i=se(e);return{activate:n=>{let{id:l,activated:a,children:s,...r}=n;return l=L(l),s.has(l)?a:i.activate({id:l,activated:a,children:s,...r})},in:i.in,out:i.out}},Ft=e=>{const i=Ue(e);return{activate:n=>{let{id:l,activated:a,children:s,...r}=n;return l=L(l),s.has(l)?a:i.activate({id:l,activated:a,children:s,...r})},in:i.in,out:i.out}},jt={open:e=>{let{id:i,value:t,opened:n,parents:l}=e;if(t){const a=new Set;a.add(i);let s=l.get(i);for(;s!=null;)a.add(s),s=l.get(s);return a}else return n.delete(i),n},select:()=>null},He={open:e=>{let{id:i,value:t,opened:n,parents:l}=e;if(t){let a=l.get(i);for(n.add(i);a!=null&&a!==i;)n.add(a),a=l.get(a);return n}else n.delete(i);return n},select:()=>null},$t={open:He.open,select:e=>{let{id:i,value:t,opened:n,parents:l}=e;if(!t)return n;const a=[];let s=l.get(i);for(;s!=null;)a.push(s),s=l.get(s);return new Set(a)}},re=e=>{const i={select:t=>{let{id:n,value:l,selected:a}=t;if(n=L(n),e&&!l){const s=Array.from(a.entries()).reduce((r,v)=>{let[f,m]=v;return m==="on"&&r.push(f),r},[]);if(s.length===1&&s[0]===n)return a}return a.set(n,l?"on":"off"),a},in:(t,n,l)=>{let a=new Map;for(const s of t||[])a=i.select({id:s,value:!0,selected:new Map(a),children:n,parents:l});return a},out:t=>{const n=[];for(const[l,a]of t.entries())a==="on"&&n.push(l);return n}};return i},qe=e=>{const i=re(e);return{select:n=>{let{selected:l,id:a,...s}=n;a=L(a);const r=l.has(a)?new Map([[a,l.get(a)]]):new Map;return i.select({...s,id:a,selected:r})},in:(n,l,a)=>{let s=new Map;return n!=null&&n.length&&(s=i.in(n.slice(0,1),l,a)),s},out:(n,l,a)=>i.out(n,l,a)}},_t=e=>{const i=re(e);return{select:n=>{let{id:l,selected:a,children:s,...r}=n;return l=L(l),s.has(l)?a:i.select({id:l,selected:a,children:s,...r})},in:i.in,out:i.out}},Gt=e=>{const i=qe(e);return{select:n=>{let{id:l,selected:a,children:s,...r}=n;return l=L(l),s.has(l)?a:i.select({id:l,selected:a,children:s,...r})},in:i.in,out:i.out}},Nt=e=>{const i={select:t=>{let{id:n,value:l,selected:a,children:s,parents:r}=t;n=L(n);const v=new Map(a),f=[n];for(;f.length;){const S=f.shift();a.set(S,l?"on":"off"),s.has(S)&&f.push(...s.get(S))}let m=r.get(n);for(;m;){const S=s.get(m),g=S.every(c=>a.get(c)==="on"),o=S.every(c=>!a.has(c)||a.get(c)==="off");a.set(m,g?"on":o?"off":"indeterminate"),m=r.get(m)}return e&&!l&&Array.from(a.entries()).reduce((g,o)=>{let[c,u]=o;return u==="on"&&g.push(c),g},[]).length===0?v:a},in:(t,n,l)=>{let a=new Map;for(const s of t||[])a=i.select({id:s,value:!0,selected:new Map(a),children:n,parents:l});return a},out:(t,n)=>{const l=[];for(const[a,s]of t.entries())s==="on"&&!n.has(a)&&l.push(a);return l}};return i},G=Symbol.for("vuetify:nested"),Xe={id:F(),root:{register:()=>null,unregister:()=>null,parents:P(new Map),children:P(new Map),open:()=>null,openOnSelect:()=>null,activate:()=>null,select:()=>null,activatable:P(!1),selectable:P(!1),opened:P(new Set),activated:P(new Set),selected:P(new Map),selectedValues:P([])}},Rt=x({activatable:Boolean,selectable:Boolean,activeStrategy:[String,Function,Object],selectStrategy:[String,Function,Object],openStrategy:[String,Object],opened:null,activated:null,selected:null,mandatory:Boolean},"nested"),Kt=e=>{let i=!1;const t=P(new Map),n=P(new Map),l=X(e,"opened",e.opened,o=>new Set(o),o=>[...o.values()]),a=h(()=>{if(typeof e.activeStrategy=="object")return e.activeStrategy;if(typeof e.activeStrategy=="function")return e.activeStrategy(e.mandatory);switch(e.activeStrategy){case"leaf":return Dt(e.mandatory);case"single-leaf":return Ft(e.mandatory);case"independent":return se(e.mandatory);case"single-independent":default:return Ue(e.mandatory)}}),s=h(()=>{if(typeof e.selectStrategy=="object")return e.selectStrategy;if(typeof e.selectStrategy=="function")return e.selectStrategy(e.mandatory);switch(e.selectStrategy){case"single-leaf":return Gt(e.mandatory);case"leaf":return _t(e.mandatory);case"independent":return re(e.mandatory);case"single-independent":return qe(e.mandatory);case"classic":default:return Nt(e.mandatory)}}),r=h(()=>{if(typeof e.openStrategy=="object")return e.openStrategy;switch(e.openStrategy){case"list":return $t;case"single":return jt;case"multiple":default:return He}}),v=X(e,"activated",e.activated,o=>a.value.in(o,t.value,n.value),o=>a.value.out(o,t.value,n.value)),f=X(e,"selected",e.selected,o=>s.value.in(o,t.value,n.value),o=>s.value.out(o,t.value,n.value));Ae(()=>{i=!0});function m(o){const c=[];let u=o;for(;u!=null;)c.unshift(u),u=n.value.get(u);return c}const S=lt("nested"),g={id:F(),root:{opened:l,activatable:w(e,"activatable"),selectable:w(e,"selectable"),activated:v,selected:f,selectedValues:h(()=>{const o=[];for(const[c,u]of f.value.entries())u==="on"&&o.push(c);return o}),register:(o,c,u)=>{c&&o!==c&&n.value.set(o,c),u&&t.value.set(o,[]),c!=null&&t.value.set(c,[...t.value.get(c)||[],o])},unregister:o=>{if(i)return;t.value.delete(o);const c=n.value.get(o);if(c){const u=t.value.get(c)??[];t.value.set(c,u.filter(d=>d!==o))}n.value.delete(o)},open:(o,c,u)=>{S.emit("click:open",{id:o,value:c,path:m(o),event:u});const d=r.value.open({id:o,value:c,opened:new Set(l.value),children:t.value,parents:n.value,event:u});d&&(l.value=d)},openOnSelect:(o,c,u)=>{const d=r.value.select({id:o,value:c,selected:new Map(f.value),opened:new Set(l.value),children:t.value,parents:n.value,event:u});d&&(l.value=d)},select:(o,c,u)=>{S.emit("click:select",{id:o,value:c,path:m(o),event:u});const d=s.value.select({id:o,value:c,selected:new Map(f.value),children:t.value,parents:n.value,event:u});d&&(f.value=d),g.root.openOnSelect(o,c,u)},activate:(o,c,u)=>{if(!e.activatable)return g.root.select(o,!0,u);S.emit("click:activate",{id:o,value:c,path:m(o),event:u});const d=a.value.activate({id:o,value:c,activated:new Set(v.value),children:t.value,parents:n.value,event:u});d&&(v.value=d)},children:t,parents:n}};return R(G,g),g.root},Ye=(e,i)=>{const t=N(G,Xe),n=Symbol(Pe()),l=h(()=>e.value!==void 0?e.value:n),a={...t,id:l,open:(s,r)=>t.root.open(l.value,s,r),openOnSelect:(s,r)=>t.root.openOnSelect(l.value,s,r),isOpen:h(()=>t.root.opened.value.has(l.value)),parent:h(()=>t.root.parents.value.get(l.value)),activate:(s,r)=>t.root.activate(l.value,s,r),isActivated:h(()=>t.root.activated.value.has(L(l.value))),select:(s,r)=>t.root.select(l.value,s,r),isSelected:h(()=>t.root.selected.value.get(L(l.value))==="on"),isIndeterminate:h(()=>t.root.selected.value.get(l.value)==="indeterminate"),isLeaf:h(()=>!t.root.children.value.get(l.value)),isGroupActivator:t.isGroupActivator};return!t.isGroupActivator&&t.root.register(l.value,t.id.value,i),Ae(()=>{!t.isGroupActivator&&t.root.unregister(l.value)}),i&&R(G,a),a},Ut=()=>{const e=N(G,Xe);R(G,{...e,isGroupActivator:!0})},Ht=it({name:"VListGroupActivator",setup(e,i){let{slots:t}=i;return Ut(),()=>{var n;return(n=t.default)==null?void 0:n.call(t)}}}),qt=x({activeColor:String,baseColor:String,color:String,collapseIcon:{type:D,default:"$collapse"},expandIcon:{type:D,default:"$expand"},prependIcon:D,appendIcon:D,fluid:Boolean,subgroup:Boolean,title:String,value:null,...K(),...U()},"VListGroup"),Ce=B()({name:"VListGroup",props:qt(),setup(e,i){let{slots:t}=i;const{isOpen:n,open:l,id:a}=Ye(w(e,"value"),!0),s=h(()=>`v-list-group--id-${String(a.value)}`),r=Ke(),{isBooted:v}=Ct();function f(o){o.stopPropagation(),l(!n.value,o)}const m=h(()=>({onClick:f,class:"v-list-group__header",id:s.value})),S=h(()=>n.value?e.collapseIcon:e.expandIcon),g=h(()=>({VListItem:{active:n.value,activeColor:e.activeColor,baseColor:e.baseColor,color:e.color,prependIcon:e.prependIcon||e.subgroup&&S.value,appendIcon:e.appendIcon||!e.subgroup&&S.value,title:e.title,value:e.value}}));return j(()=>y(e.tag,{class:["v-list-group",{"v-list-group--prepend":r==null?void 0:r.hasPrepend.value,"v-list-group--fluid":e.fluid,"v-list-group--subgroup":e.subgroup,"v-list-group--open":n.value},e.class],style:e.style},{default:()=>[t.activator&&y(Y,{defaults:g.value},{default:()=>[y(Ht,null,{default:()=>[t.activator({props:m.value,isOpen:n.value})]})]}),y(Vt,{transition:{component:wt},disabled:!v.value},{default:()=>{var o;return[Ie(y("div",{class:"v-list-group__items",role:"group","aria-labelledby":s.value},[(o=t.default)==null?void 0:o.call(t)]),[[st,n.value]])]}})]})),{isOpen:n}}}),Xt=x({opacity:[Number,String],...K(),...U()},"VListItemSubtitle"),Yt=B()({name:"VListItemSubtitle",props:Xt(),setup(e,i){let{slots:t}=i;return j(()=>y(e.tag,{class:["v-list-item-subtitle",e.class],style:[{"--v-list-item-subtitle-opacity":e.opacity},e.style]},t)),{}}}),zt=At("v-list-item-title"),Wt=x({active:{type:Boolean,default:void 0},activeClass:String,activeColor:String,appendAvatar:String,appendIcon:D,baseColor:String,disabled:Boolean,lines:[Boolean,String],link:{type:Boolean,default:void 0},nav:Boolean,prependAvatar:String,prependIcon:D,ripple:{type:[Boolean,Object],default:!0},slim:Boolean,subtitle:[String,Number],title:[String,Number],value:null,onClick:$(),onClickOnce:$(),...Le(),...K(),...xe(),...Oe(),...Ee(),...Te(),...rt(),...U(),...Be(),...Me({variant:"text"})},"VListItem"),ke=B()({name:"VListItem",directives:{Ripple:ot},props:Wt(),emits:{click:e=>!0},setup(e,i){let{attrs:t,slots:n,emit:l}=i;const a=ut(e,t),s=h(()=>e.value===void 0?a.href.value:e.value),{activate:r,isActivated:v,select:f,isSelected:m,isIndeterminate:S,isGroupActivator:g,root:o,parent:c,openOnSelect:u}=Ye(s,!1),d=Ke(),b=h(()=>{var p;return e.active!==!1&&(e.active||((p=a.isActive)==null?void 0:p.value)||(o.activatable.value?v.value:m.value))}),C=h(()=>e.link!==!1&&a.isLink.value),k=h(()=>!e.disabled&&e.link!==!1&&(e.link||a.isClickable.value||!!d&&(o.selectable.value||o.activatable.value||e.value!=null))),A=h(()=>e.rounded||e.nav),E=h(()=>e.color??e.activeColor),z=h(()=>({color:b.value?E.value??e.baseColor:e.baseColor,variant:e.variant}));De(()=>{var p;return(p=a.isActive)==null?void 0:p.value},p=>{p&&c.value!=null&&o.open(c.value,!0),p&&u(p)},{immediate:!0});const{themeClasses:W}=Fe(e),{borderClasses:J}=je(e),{colorClasses:Z,colorStyles:Q,variantClasses:T}=ct(z),{densityClasses:V}=$e(e),{dimensionStyles:M}=_e(e),{elevationClasses:Ze}=Ge(e),{roundedClasses:Qe}=Ne(A),et=h(()=>e.lines?`v-list-item--${e.lines}-line`:void 0),ee=h(()=>({isActive:b.value,select:f,isSelected:m.value,isIndeterminate:S.value}));function oe(p){var H;l("click",p),k.value&&((H=a.navigate)==null||H.call(a,p),!g&&(o.activatable.value?r(!v.value,p):(o.selectable.value||e.value!=null)&&f(!m.value,p)))}function tt(p){(p.key==="Enter"||p.key===" ")&&(p.preventDefault(),oe(p))}return j(()=>{const p=C.value?"a":e.tag,H=n.title||e.title!=null,nt=n.subtitle||e.subtitle!=null,ue=!!(e.appendAvatar||e.appendIcon),at=!!(ue||n.append),ce=!!(e.prependAvatar||e.prependIcon),te=!!(ce||n.prepend);return d==null||d.updateHasPrepend(te),e.activeColor&&dt("active-color",["color","base-color"]),Ie(y(p,{class:["v-list-item",{"v-list-item--active":b.value,"v-list-item--disabled":e.disabled,"v-list-item--link":k.value,"v-list-item--nav":e.nav,"v-list-item--prepend":!te&&(d==null?void 0:d.hasPrepend.value),"v-list-item--slim":e.slim,[`${e.activeClass}`]:e.activeClass&&b.value},W.value,J.value,Z.value,V.value,Ze.value,et.value,Qe.value,T.value,e.class],style:[Q.value,M.value,e.style],href:a.href.value,tabindex:k.value?d?-2:0:void 0,onClick:oe,onKeydown:k.value&&!C.value&&tt},{default:()=>{var de;return[ft(k.value||b.value,"v-list-item"),te&&y("div",{key:"prepend",class:"v-list-item__prepend"},[n.prepend?y(Y,{key:"prepend-defaults",disabled:!ce,defaults:{VAvatar:{density:e.density,image:e.prependAvatar},VIcon:{density:e.density,icon:e.prependIcon},VListItemAction:{start:!0}}},{default:()=>{var I;return[(I=n.prepend)==null?void 0:I.call(n,ee.value)]}}):y(fe,null,[e.prependAvatar&&y(ge,{key:"prepend-avatar",density:e.density,image:e.prependAvatar},null),e.prependIcon&&y(me,{key:"prepend-icon",density:e.density,icon:e.prependIcon},null)]),y("div",{class:"v-list-item__spacer"},null)]),y("div",{class:"v-list-item__content","data-no-activator":""},[H&&y(zt,{key:"title"},{default:()=>{var I;return[((I=n.title)==null?void 0:I.call(n,{title:e.title}))??e.title]}}),nt&&y(Yt,{key:"subtitle"},{default:()=>{var I;return[((I=n.subtitle)==null?void 0:I.call(n,{subtitle:e.subtitle}))??e.subtitle]}}),(de=n.default)==null?void 0:de.call(n,ee.value)]),at&&y("div",{key:"append",class:"v-list-item__append"},[n.append?y(Y,{key:"append-defaults",disabled:!ue,defaults:{VAvatar:{density:e.density,image:e.appendAvatar},VIcon:{density:e.density,icon:e.appendIcon},VListItemAction:{end:!0}}},{default:()=>{var I;return[(I=n.append)==null?void 0:I.call(n,ee.value)]}}):y(fe,null,[e.appendIcon&&y(me,{key:"append-icon",density:e.density,icon:e.appendIcon},null),e.appendAvatar&&y(ge,{key:"append-avatar",density:e.density,image:e.appendAvatar},null)]),y("div",{class:"v-list-item__spacer"},null)])]}}),[[vt("ripple"),k.value&&e.ripple]])}),{activate:r,isActivated:v,isGroupActivator:g,isSelected:m,list:d,select:f}}}),Jt=x({color:String,inset:Boolean,sticky:Boolean,title:String,...K(),...U()},"VListSubheader"),Zt=B()({name:"VListSubheader",props:Jt(),setup(e,i){let{slots:t}=i;const{textColorClasses:n,textColorStyles:l}=mt(w(e,"color"));return j(()=>{const a=!!(t.default||e.title);return y(e.tag,{class:["v-list-subheader",{"v-list-subheader--inset":e.inset,"v-list-subheader--sticky":e.sticky},n.value,e.class],style:[{textColorStyles:l},e.style]},{default:()=>{var s;return[a&&y("div",{class:"v-list-subheader__text"},[((s=t.default)==null?void 0:s.call(t))??e.title])]}})}),{}}}),Qt=x({items:Array,returnObject:Boolean},"VListChildren"),ze=B()({name:"VListChildren",props:Qt(),setup(e,i){let{slots:t}=i;return Re(),()=>{var n,l;return((n=t.default)==null?void 0:n.call(t))??((l=e.items)==null?void 0:l.map(a=>{var g,o;let{children:s,props:r,type:v,raw:f}=a;if(v==="divider")return((g=t.divider)==null?void 0:g.call(t,{props:r}))??y(kt,r,null);if(v==="subheader")return((o=t.subheader)==null?void 0:o.call(t,{props:r}))??y(Zt,r,null);const m={subtitle:t.subtitle?c=>{var u;return(u=t.subtitle)==null?void 0:u.call(t,{...c,item:f})}:void 0,prepend:t.prepend?c=>{var u;return(u=t.prepend)==null?void 0:u.call(t,{...c,item:f})}:void 0,append:t.append?c=>{var u;return(u=t.append)==null?void 0:u.call(t,{...c,item:f})}:void 0,title:t.title?c=>{var u;return(u=t.title)==null?void 0:u.call(t,{...c,item:f})}:void 0},S=Ce.filterProps(r);return s?y(Ce,_({value:r==null?void 0:r.value},S),{activator:c=>{let{props:u}=c;const d={...r,...u,value:e.returnObject?f:r.value};return t.header?t.header({props:d}):y(ke,d,m)},default:()=>y(ze,{items:s,returnObject:e.returnObject},t)}):t.item?t.item({props:r}):y(ke,_(r,{value:e.returnObject?f:r.value}),m)}))}}}),en=x({items:{type:Array,default:()=>[]},itemTitle:{type:[String,Array,Function],default:"title"},itemValue:{type:[String,Array,Function],default:"value"},itemChildren:{type:[Boolean,String,Array,Function],default:"children"},itemProps:{type:[Boolean,String,Array,Function],default:"props"},returnObject:Boolean,valueComparator:{type:Function,default:yt}},"list-items");function le(e,i){const t=O(i,e.itemTitle,i),n=O(i,e.itemValue,t),l=O(i,e.itemChildren),a=e.itemProps===!0?typeof i=="object"&&i!=null&&!Array.isArray(i)?"children"in i?ie(i,["children"]):i:void 0:O(i,e.itemProps),s={title:t,value:n,...a};return{title:String(s.title??""),value:s.value,props:s,children:Array.isArray(l)?We(e,l):void 0,raw:i}}function We(e,i){const t=[];for(const n of i)t.push(le(e,n));return t}function yn(e){const i=h(()=>We(e,e.items)),t=h(()=>i.value.some(a=>a.value===null));function n(a){return t.value||(a=a.filter(s=>s!==null)),a.map(s=>e.returnObject&&typeof s=="string"?le(e,s):i.value.find(r=>e.valueComparator(s,r.value))||le(e,s))}function l(a){return e.returnObject?a.map(s=>{let{raw:r}=s;return r}):a.map(s=>{let{value:r}=s;return r})}return{items:i,transformIn:n,transformOut:l}}function tn(e){return typeof e=="string"||typeof e=="number"||typeof e=="boolean"}function nn(e,i){const t=O(i,e.itemType,"item"),n=tn(i)?i:O(i,e.itemTitle),l=O(i,e.itemValue,void 0),a=O(i,e.itemChildren),s=e.itemProps===!0?ie(i,["children"]):O(i,e.itemProps),r={title:n,value:l,...s};return{type:t,title:r.title,value:r.value,props:r,children:t==="item"&&a?Je(e,a):void 0,raw:i}}function Je(e,i){const t=[];for(const n of i)t.push(nn(e,n));return t}function an(e){return{items:h(()=>Je(e,e.items))}}const ln=x({baseColor:String,activeColor:String,activeClass:String,bgColor:String,disabled:Boolean,expandIcon:String,collapseIcon:String,lines:{type:[Boolean,String],default:"one"},slim:Boolean,nav:Boolean,"onClick:open":$(),"onClick:select":$(),"onUpdate:opened":$(),...Rt({selectStrategy:"single-leaf",openStrategy:"list"}),...Le(),...K(),...xe(),...Oe(),...Ee(),itemType:{type:String,default:"type"},...en(),...Te(),...U(),...Be(),...Me({variant:"text"})},"VList"),gn=B()({name:"VList",props:ln(),emits:{"update:selected":e=>!0,"update:activated":e=>!0,"update:opened":e=>!0,"click:open":e=>!0,"click:activate":e=>!0,"click:select":e=>!0},setup(e,i){let{slots:t}=i;const{items:n}=an(e),{themeClasses:l}=Fe(e),{backgroundColorClasses:a,backgroundColorStyles:s}=gt(w(e,"bgColor")),{borderClasses:r}=je(e),{densityClasses:v}=$e(e),{dimensionStyles:f}=_e(e),{elevationClasses:m}=Ge(e),{roundedClasses:S}=Ne(e),{children:g,open:o,parents:c,select:u}=Kt(e),d=h(()=>e.lines?`v-list--${e.lines}-line`:void 0),b=w(e,"activeColor"),C=w(e,"baseColor"),k=w(e,"color");Re(),ht({VListGroup:{activeColor:b,baseColor:C,color:k,expandIcon:w(e,"expandIcon"),collapseIcon:w(e,"collapseIcon")},VListItem:{activeClass:w(e,"activeClass"),activeColor:b,baseColor:C,color:k,density:w(e,"density"),disabled:w(e,"disabled"),lines:w(e,"lines"),nav:w(e,"nav"),slim:w(e,"slim"),variant:w(e,"variant")}});const A=F(!1),E=P();function z(V){A.value=!0}function W(V){A.value=!1}function J(V){var M;!A.value&&!(V.relatedTarget&&((M=E.value)!=null&&M.contains(V.relatedTarget)))&&T()}function Z(V){const M=V.target;if(!(!E.value||["INPUT","TEXTAREA"].includes(M.tagName))){if(V.key==="ArrowDown")T("next");else if(V.key==="ArrowUp")T("prev");else if(V.key==="Home")T("first");else if(V.key==="End")T("last");else return;V.preventDefault()}}function Q(V){A.value=!0}function T(V){if(E.value)return ne(E.value,V)}return j(()=>y(e.tag,{ref:E,class:["v-list",{"v-list--disabled":e.disabled,"v-list--nav":e.nav,"v-list--slim":e.slim},l.value,a.value,r.value,v.value,m.value,d.value,S.value,e.class],style:[s.value,f.value,e.style],tabindex:e.disabled||A.value?-1:0,role:"listbox","aria-activedescendant":void 0,onFocusin:z,onFocusout:W,onFocus:J,onKeydown:Z,onMousedown:Q},{default:()=>[y(ze,{items:n.value,returnObject:e.returnObject},t)]})),{open:o,select:u,focus:T,children:g,parents:c}}}),sn=x({id:String,...ie(Pt({closeDelay:250,closeOnContentClick:!0,locationStrategy:"connected",openDelay:300,scrim:!1,scrollStrategy:"reposition",transition:{component:Mt}}),["absolute"])},"VMenu"),hn=B()({name:"VMenu",props:sn(),emits:{"update:modelValue":e=>!0},setup(e,i){let{slots:t}=i;const n=X(e,"modelValue"),{scopeId:l}=Tt(),a=Pe(),s=h(()=>e.id||`v-menu-${a}`),r=P(),v=N(he,null),f=F(0);R(he,{register(){++f.value},unregister(){--f.value},closeParents(u){setTimeout(()=>{var d;!f.value&&!e.persistent&&(u==null||(d=r.value)!=null&&d.contentEl&&!St(u,r.value.contentEl))&&(n.value=!1,v==null||v.closeParents())},40)}});async function m(u){var C,k,A;const d=u.relatedTarget,b=u.target;await bt(),n.value&&d!==b&&((C=r.value)!=null&&C.contentEl)&&((k=r.value)!=null&&k.globalTop)&&![document,r.value.contentEl].includes(b)&&!r.value.contentEl.contains(b)&&((A=ye(r.value.contentEl)[0])==null||A.focus())}De(n,u=>{u?(v==null||v.register(),document.addEventListener("focusin",m,{once:!0})):(v==null||v.unregister(),document.removeEventListener("focusin",m))});function S(u){v==null||v.closeParents(u)}function g(u){var d,b,C;if(!e.disabled)if(u.key==="Tab"||u.key==="Enter"&&!e.closeOnContentClick){if(u.key==="Enter"&&(u.target instanceof HTMLTextAreaElement||u.target instanceof HTMLInputElement&&u.target.closest("form")))return;u.key==="Enter"&&u.preventDefault(),pt(ye((d=r.value)==null?void 0:d.contentEl,!1),u.shiftKey?"prev":"next",A=>A.tabIndex>=0)||(n.value=!1,(C=(b=r.value)==null?void 0:b.activatorEl)==null||C.focus())}else["Enter"," "].includes(u.key)&&e.closeOnContentClick&&(n.value=!1,v==null||v.closeParents())}function o(u){var b;if(e.disabled)return;const d=(b=r.value)==null?void 0:b.contentEl;d&&n.value?u.key==="ArrowDown"?(u.preventDefault(),ne(d,"next")):u.key==="ArrowUp"&&(u.preventDefault(),ne(d,"prev")):["ArrowDown","ArrowUp"].includes(u.key)&&(n.value=!0,u.preventDefault(),setTimeout(()=>setTimeout(()=>o(u))))}const c=h(()=>_({"aria-haspopup":"menu","aria-expanded":String(n.value),"aria-owns":s.value,onKeydown:o},e.activatorProps));return j(()=>{const u=Se.filterProps(e);return y(Se,_({ref:r,id:s.value,class:["v-menu",e.class],style:e.style},u,{modelValue:n.value,"onUpdate:modelValue":d=>n.value=d,absolute:!0,activatorProps:c.value,"onClick:outside":S,onKeydown:g},l),{activator:t.activator,default:function(){for(var d=arguments.length,b=new Array(d),C=0;C<d;C++)b[C]=arguments[C];return y(Y,{root:"VMenu"},{default:()=>{var k;return[(k=t.default)==null?void 0:k.call(t,...b)]}})}})}),Et({id:s,ΨopenChildren:f},r)}});export{hn as V,gn as a,ke as b,zt as c,Yt as d,Mt as e,en as m,yn as u};
