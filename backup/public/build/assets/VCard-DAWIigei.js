import{B as l,v as s,a4 as z,J as c,h as n,q as y,z as C,I as m,a7 as x,F as p,V as h,an as V,a6 as J,av as U,a8 as G,aS as K,x as Q,bB as W,y as X,aa as Y,A as Z,a1 as $,ac as ee,a2 as ae,ad as te,ae as ne,af as de,ax as ie,ag as le,aT as se,H as ce,bC as re,D as ue,ak as oe,b as S,L as ve,al as me,aU as ye,am as be}from"./main-B6lduhDs.js";import{c as ge,V as P}from"./VAvatar-DCRWcC4u.js";import{V as ke}from"./VImg-CuSWVABe.js";const fe=l()({name:"VCardActions",props:s(),setup(e,d){let{slots:t}=d;return z({VBtn:{slim:!0,variant:"text"}}),c(()=>{var a;return n("div",{class:["v-card-actions",e.class],style:e.style},[(a=t.default)==null?void 0:a.call(t)])}),{}}}),Ve=y({opacity:[Number,String],...s(),...C()},"VCardSubtitle"),Ce=l()({name:"VCardSubtitle",props:Ve(),setup(e,d){let{slots:t}=d;return c(()=>n(e.tag,{class:["v-card-subtitle",e.class],style:[{"--v-card-subtitle-opacity":e.opacity},e.style]},t)),{}}}),Ie=ge("v-card-title"),Ae=y({appendAvatar:String,appendIcon:m,prependAvatar:String,prependIcon:m,subtitle:[String,Number],title:[String,Number],...s(),...x()},"VCardItem"),pe=l()({name:"VCardItem",props:Ae(),setup(e,d){let{slots:t}=d;return c(()=>{var u;const a=!!(e.prependAvatar||e.prependIcon),b=!!(a||t.prepend),r=!!(e.appendAvatar||e.appendIcon),g=!!(r||t.append),k=!!(e.title!=null||t.title),f=!!(e.subtitle!=null||t.subtitle);return n("div",{class:["v-card-item",e.class],style:e.style},[b&&n("div",{key:"prepend",class:"v-card-item__prepend"},[t.prepend?n(V,{key:"prepend-defaults",disabled:!a,defaults:{VAvatar:{density:e.density,image:e.prependAvatar},VIcon:{density:e.density,icon:e.prependIcon}}},t.prepend):n(p,null,[e.prependAvatar&&n(P,{key:"prepend-avatar",density:e.density,image:e.prependAvatar},null),e.prependIcon&&n(h,{key:"prepend-icon",density:e.density,icon:e.prependIcon},null)])]),n("div",{class:"v-card-item__content"},[k&&n(Ie,{key:"title"},{default:()=>{var i;return[((i=t.title)==null?void 0:i.call(t))??e.title]}}),f&&n(Ce,{key:"subtitle"},{default:()=>{var i;return[((i=t.subtitle)==null?void 0:i.call(t))??e.subtitle]}}),(u=t.default)==null?void 0:u.call(t)]),g&&n("div",{key:"append",class:"v-card-item__append"},[t.append?n(V,{key:"append-defaults",disabled:!r,defaults:{VAvatar:{density:e.density,image:e.appendAvatar},VIcon:{density:e.density,icon:e.appendIcon}}},t.append):n(p,null,[e.appendIcon&&n(h,{key:"append-icon",density:e.density,icon:e.appendIcon},null),e.appendAvatar&&n(P,{key:"append-avatar",density:e.density,image:e.appendAvatar},null)])])])}),{}}}),he=y({opacity:[Number,String],...s(),...C()},"VCardText"),Se=l()({name:"VCardText",props:he(),setup(e,d){let{slots:t}=d;return c(()=>n(e.tag,{class:["v-card-text",e.class],style:[{"--v-card-text-opacity":e.opacity},e.style]},t)),{}}}),Pe=y({appendAvatar:String,appendIcon:m,disabled:Boolean,flat:Boolean,hover:Boolean,image:String,link:{type:Boolean,default:void 0},prependAvatar:String,prependIcon:m,ripple:{type:[Boolean,Object],default:!0},subtitle:[String,Number],text:[String,Number],title:[String,Number],...J(),...s(),...x(),...U(),...G(),...K(),...Q(),...W(),...X(),...Y(),...C(),...Z(),...$({variant:"elevated"})},"VCard"),De=l()({name:"VCard",directives:{Ripple:ee},props:Pe(),setup(e,d){let{attrs:t,slots:a}=d;const{themeClasses:b}=ae(e),{borderClasses:r}=te(e),{colorClasses:g,colorStyles:k,variantClasses:f}=ne(e),{densityClasses:u}=de(e),{dimensionStyles:i}=ie(e),{elevationClasses:T}=le(e),{loaderClasses:B}=se(e),{locationStyles:D}=ce(e),{positionClasses:L}=re(e),{roundedClasses:N}=ue(e),o=oe(e,t),_=S(()=>e.link!==!1&&o.isLink.value),v=S(()=>!e.disabled&&e.link!==!1&&(e.link||o.isClickable.value));return c(()=>{const R=_.value?"a":e.tag,F=!!(a.title||e.title!=null),E=!!(a.subtitle||e.subtitle!=null),H=F||E,M=!!(a.append||e.appendAvatar||e.appendIcon),O=!!(a.prepend||e.prependAvatar||e.prependIcon),j=!!(a.image||e.image),q=H||O||M,w=!!(a.text||e.text!=null);return ve(n(R,{class:["v-card",{"v-card--disabled":e.disabled,"v-card--flat":e.flat,"v-card--hover":e.hover&&!(e.disabled||e.flat),"v-card--link":v.value},b.value,r.value,g.value,u.value,T.value,B.value,L.value,N.value,f.value,e.class],style:[k.value,i.value,D.value,e.style],href:o.href.value,onClick:v.value&&o.navigate,tabindex:e.disabled?-1:void 0},{default:()=>{var I;return[j&&n("div",{key:"image",class:"v-card__image"},[a.image?n(V,{key:"image-defaults",disabled:!e.image,defaults:{VImg:{cover:!0,src:e.image}}},a.image):n(ke,{key:"image-img",cover:!0,src:e.image},null)]),n(ye,{name:"v-card",active:!!e.loading,color:typeof e.loading=="boolean"?void 0:e.loading},{default:a.loader}),q&&n(pe,{key:"item",prependAvatar:e.prependAvatar,prependIcon:e.prependIcon,title:e.title,subtitle:e.subtitle,appendAvatar:e.appendAvatar,appendIcon:e.appendIcon},{default:a.item,prepend:a.prepend,title:a.title,subtitle:a.subtitle,append:a.append}),w&&n(Se,{key:"text"},{default:()=>{var A;return[((A=a.text)==null?void 0:A.call(a))??e.text]}}),(I=a.default)==null?void 0:I.call(a),a.actions&&n(fe,null,{default:a.actions}),be(v.value,"v-card")]}}),[[me("ripple"),v.value&&e.ripple]])}),{}}});export{Se as V,De as a,pe as b,Ie as c,Ce as d,fe as e};
