import{q as S,I as B,a2 as K,v as R,aa as z,A as Q,B as A,al as I,a_ as M,b as o,aL as W,ay as X,a7 as Y,t as i,J as F,h as d,af as Z,s as $,r as p,bs as ee,N as P,L as le,ao as ae,F as te,V as ne,aM as oe,ai as ue,aI as x,G as ie,C as re,aK as ce,aw as se,aH as de}from"./main-CK36iqPX.js";import{d as ve}from"./VTextField-C3T1N3mN.js";const N=Symbol.for("vuetify:selection-control-group"),O=S({color:String,disabled:{type:Boolean,default:null},defaultsTarget:String,error:Boolean,id:String,inline:Boolean,falseIcon:B,trueIcon:B,ripple:{type:[Boolean,Object],default:!0},multiple:{type:Boolean,default:null},name:String,readonly:{type:Boolean,default:null},modelValue:null,type:String,valueComparator:{type:Function,default:K},...R(),...z(),...Q()},"SelectionControlGroup"),fe=S({...O({defaultsTarget:"VSelectionControl"})},"VSelectionControlGroup");A()({name:"VSelectionControlGroup",props:fe(),emits:{"update:modelValue":e=>!0},setup(e,u){let{slots:c}=u;const l=I(e,"modelValue"),t=M(),v=o(()=>e.id||`v-selection-control-group-${t}`),r=o(()=>e.name||v.value),a=new Set;return W(N,{modelValue:l,forceUpdate:()=>{a.forEach(n=>n())},onForceUpdate:n=>{a.add(n),X(()=>{a.delete(n)})}}),Y({[e.defaultsTarget]:{color:i(e,"color"),disabled:i(e,"disabled"),density:i(e,"density"),error:i(e,"error"),inline:i(e,"inline"),modelValue:l,multiple:o(()=>!!e.multiple||e.multiple==null&&Array.isArray(l.value)),name:r,falseIcon:i(e,"falseIcon"),trueIcon:i(e,"trueIcon"),readonly:i(e,"readonly"),ripple:i(e,"ripple"),type:i(e,"type"),valueComparator:i(e,"valueComparator")}}),F(()=>{var n;return d("div",{class:["v-selection-control-group",{"v-selection-control-group--inline":e.inline},e.class],style:e.style,role:e.type==="radio"?"radiogroup":void 0},[(n=c.default)==null?void 0:n.call(c)])}),{}}});const j=S({label:String,baseColor:String,trueValue:null,falseValue:null,value:null,...R(),...O()},"VSelectionControl");function me(e){const u=oe(N,void 0),{densityClasses:c}=ue(e),l=I(e,"modelValue"),t=o(()=>e.trueValue!==void 0?e.trueValue:e.value!==void 0?e.value:!0),v=o(()=>e.falseValue!==void 0?e.falseValue:!1),r=o(()=>!!e.multiple||e.multiple==null&&Array.isArray(l.value)),a=o({get(){const y=u?u.modelValue.value:l.value;return r.value?x(y).some(s=>e.valueComparator(s,t.value)):e.valueComparator(y,t.value)},set(y){if(e.readonly)return;const s=y?t.value:v.value;let b=s;r.value&&(b=y?[...x(l.value),s]:x(l.value).filter(f=>!e.valueComparator(f,t.value))),u?u.modelValue.value=b:l.value=b}}),{textColorClasses:n,textColorStyles:C}=ie(o(()=>{if(!(e.error||e.disabled))return a.value?e.color:e.baseColor})),{backgroundColorClasses:V,backgroundColorStyles:k}=re(o(()=>a.value&&!e.error&&!e.disabled?e.color:e.baseColor)),h=o(()=>a.value?e.trueIcon:e.falseIcon);return{group:u,densityClasses:c,trueValue:t,falseValue:v,model:a,textColorClasses:n,textColorStyles:C,backgroundColorClasses:V,backgroundColorStyles:k,icon:h}}const L=A()({name:"VSelectionControl",directives:{Ripple:Z},inheritAttrs:!1,props:j(),emits:{"update:modelValue":e=>!0},setup(e,u){let{attrs:c,slots:l}=u;const{group:t,densityClasses:v,icon:r,model:a,textColorClasses:n,textColorStyles:C,backgroundColorClasses:V,backgroundColorStyles:k,trueValue:h}=me(e),y=M(),s=$(!1),b=$(!1),f=p(),g=o(()=>e.id||`input-${y}`),w=o(()=>!e.disabled&&!e.readonly);t==null||t.onForceUpdate(()=>{f.value&&(f.value.checked=a.value)});function G(m){w.value&&(s.value=!0,ce(m.target,":focus-visible")!==!1&&(b.value=!0))}function D(){s.value=!1,b.value=!1}function q(m){m.stopPropagation()}function E(m){if(!w.value){f.value&&(f.value.checked=a.value);return}e.readonly&&t&&se(()=>t.forceUpdate()),a.value=m.target.checked}return F(()=>{var U,_;const m=l.label?l.label({label:e.label,props:{for:g.value}}):e.label,[H,J]=ee(c),T=d("input",P({ref:f,checked:a.value,disabled:!!e.disabled,id:g.value,onBlur:D,onFocus:G,onInput:E,"aria-disabled":!!e.disabled,"aria-label":e.label,type:e.type,value:h.value,name:e.name,"aria-checked":e.type==="checkbox"?a.value:void 0},J),null);return d("div",P({class:["v-selection-control",{"v-selection-control--dirty":a.value,"v-selection-control--disabled":e.disabled,"v-selection-control--error":e.error,"v-selection-control--focused":s.value,"v-selection-control--focus-visible":b.value,"v-selection-control--inline":e.inline},v.value,e.class]},H,{style:e.style}),[d("div",{class:["v-selection-control__wrapper",n.value],style:C.value},[(U=l.default)==null?void 0:U.call(l,{backgroundColorClasses:V,backgroundColorStyles:k}),le(d("div",{class:["v-selection-control__input"]},[((_=l.input)==null?void 0:_.call(l,{model:a,textColorClasses:n,textColorStyles:C,backgroundColorClasses:V,backgroundColorStyles:k,inputNode:T,icon:r.value,props:{onFocus:G,onBlur:D,id:g.value}}))??d(te,null,[r.value&&d(ne,{key:"icon",icon:r.value},null),T])]),[[ae("ripple"),e.ripple&&[!e.disabled&&!e.readonly,null,["center","circle"]]]])]),m&&d(ve,{for:g.value,onClick:q},{default:()=>[m]})])}),{isFocused:s,input:f}}}),ye=S({indeterminate:Boolean,indeterminateIcon:{type:B,default:"$checkboxIndeterminate"},...j({falseIcon:"$checkboxOff",trueIcon:"$checkboxOn"})},"VCheckboxBtn"),Ve=A()({name:"VCheckboxBtn",props:ye(),emits:{"update:modelValue":e=>!0,"update:indeterminate":e=>!0},setup(e,u){let{slots:c}=u;const l=I(e,"indeterminate"),t=I(e,"modelValue");function v(n){l.value&&(l.value=!1)}const r=o(()=>l.value?e.indeterminateIcon:e.falseIcon),a=o(()=>l.value?e.indeterminateIcon:e.trueIcon);return F(()=>{const n=de(L.filterProps(e),["modelValue"]);return d(L,P(n,{modelValue:t.value,"onUpdate:modelValue":[C=>t.value=C,v],class:["v-checkbox-btn",e.class],style:e.style,type:"checkbox",falseIcon:r.value,trueIcon:a.value,"aria-checked":l.value?"mixed":void 0}),c)}),{}}});export{Ve as V,ye as m};