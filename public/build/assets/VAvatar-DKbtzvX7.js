import{B as i,aO as g,bB as f,v as o,X as y,q as V,I as k,aa as P,y as h,ae as C,z as S,A as z,a4 as I,a5 as A,ah as B,ai as D,D as x,ak as R,J as T,h as l,V as q,aq as F,ap as O}from"./main-CK36iqPX.js";import{V as _}from"./VImg-Djd7BO5H.js";function X(a){let r=arguments.length>1&&arguments[1]!==void 0?arguments[1]:"div",s=arguments.length>2?arguments[2]:void 0;return i()({name:s??g(f(a.replace(/__/g,"-"))),props:{tag:{type:String,default:r},...o()},setup(e,u){let{slots:t}=u;return()=>{var n;return y(e.tag,{class:[a,e.class],style:e.style},(n=t.default)==null?void 0:n.call(t))}}})}const b=V({start:Boolean,end:Boolean,icon:k,image:String,text:String,...o(),...P(),...h(),...C(),...S(),...z(),...I({variant:"flat"})},"VAvatar"),j=i()({name:"VAvatar",props:b(),setup(a,r){let{slots:s}=r;const{themeClasses:e}=A(a),{colorClasses:u,colorStyles:t,variantClasses:n}=B(a),{densityClasses:c}=D(a),{roundedClasses:m}=x(a),{sizeClasses:v,sizeStyles:d}=R(a);return T(()=>l(a.tag,{class:["v-avatar",{"v-avatar--start":a.start,"v-avatar--end":a.end},e.value,u.value,c.value,m.value,v.value,n.value,a.class],style:[t.value,d.value,a.style]},{default:()=>[s.default?l(F,{key:"content-defaults",defaults:{VImg:{cover:!0,src:a.image},VIcon:{icon:a.icon}}},{default:()=>[s.default()]}):a.image?l(_,{key:"image",src:a.image,alt:"",cover:!0},null):a.icon?l(q,{key:"icon",icon:a.icon},null):a.text,O(!1,"v-avatar")]})),{}}});export{j as V,X as c};