import{q as ie,I as Z,v as re,a8 as ue,ac as de,z as me,A as fe,B as ce,E as ve,a3 as ge,aj as pe,b as T,as as be,aJ as ye,s as _e,a_ as we,J as xe,h as e,F as C,aF as f,N as he,P as s,_ as ke,r as H,o as w,f as j,g as l,u as p,j as n,l as I,Y as S,V,L as Ve,M as Ce,X as Ie,i as F,k,e as Be}from"./main-Dr3_htUk.js";import{a as O}from"./avatar-1-BpD18F9D.js";import{a as Te}from"./index-Ckiq5CHF.js";import{a as g,V as z}from"./VRow-DSTZjXl0.js";import{a as y,b as h,c as x,V as u,d as Ae,e as M}from"./VCard-C9qo50JZ.js";import{V as A}from"./VImg-BxLvZF-2.js";import{V as R}from"./VAvatar-DRoYpovF.js";import{V as Se}from"./VSpacer-Dnb9Q9x7.js";import{V as E}from"./ssrBoot-BoH7JcAp.js";import{a as Q,c as ee,b as le,d as te}from"./VTabs-Drr-Yg0o.js";/* empty css              */import"./forwardRefs-DWGaNmQL.js";import"./scopeId-yF8hzUmo.js";const Le="/build/assets/avatar-2-DCJB4Ubn.png",Me="/build/assets/avatar-3-DBHb2r0Y.png",se="/build/assets/avatar-4-x_MPl8Kx.png",Ne="/build/assets/2-CorP6DNx.png",$e="/build/assets/1-CxILTXIE.png",Pe="/build/assets/2-CbZIFOZ3.png",Fe="/build/assets/3-BTaq5jfT.png",Ee="/build/assets/5-CFPERer_.jpg",Re="/build/assets/6-CgiBB01F.jpg",je=ie({name:String,itemAriaLabel:{type:String,default:"$vuetify.rating.ariaLabel.item"},activeColor:String,color:String,clearable:Boolean,disabled:Boolean,emptyIcon:{type:Z,default:"$ratingEmpty"},fullIcon:{type:Z,default:"$ratingFull"},halfIncrements:Boolean,hover:Boolean,length:{type:[Number,String],default:5},readonly:Boolean,modelValue:{type:[Number,String],default:0},itemLabels:Array,itemLabelPosition:{type:String,default:"top",validator:a=>["top","bottom"].includes(a)},ripple:Boolean,...re(),...ue(),...de(),...me(),...fe()},"VRating"),ae=ce()({name:"VRating",props:je(),emits:{"update:modelValue":a=>!0},setup(a,b){let{slots:i}=b;const{t:_}=ve(),{themeClasses:t}=ge(a),o=pe(a,"modelValue"),r=T(()=>be(parseFloat(o.value),0,+a.length)),W=T(()=>ye(Number(a.length),1)),q=T(()=>W.value.flatMap(d=>a.halfIncrements?[d-.5,d]:[d])),N=_e(-1),U=T(()=>q.value.map(d=>{const c=a.hover&&N.value>-1,m=r.value>=d,v=N.value>=d,B=(c?v:m)?a.fullIcon:a.emptyIcon,D=a.activeColor??a.color,P=m||v?D:a.color;return{isFilled:m,isHovered:v,icon:B,color:P}})),oe=T(()=>[0,...q.value].map(d=>{function c(){N.value=d}function m(){N.value=-1}function v(){a.disabled||a.readonly||(o.value=r.value===d&&a.clearable?0:d)}return{onMouseenter:a.hover?c:void 0,onMouseleave:a.hover?m:void 0,onClick:v}})),Y=T(()=>a.name??`v-rating-${we()}`);function $(d){var K,X;let{value:c,index:m,showStar:v=!0}=d;const{onMouseenter:L,onMouseleave:B,onClick:D}=oe.value[m+1],P=`${Y.value}-${String(c).replace(".","-")}`,G={color:(K=U.value[m])==null?void 0:K.color,density:a.density,disabled:a.disabled,icon:(X=U.value[m])==null?void 0:X.icon,ripple:a.ripple,size:a.size,variant:"plain"};return e(C,null,[e("label",{for:P,class:{"v-rating__item--half":a.halfIncrements&&c%1>0,"v-rating__item--full":a.halfIncrements&&c%1===0},onMouseenter:L,onMouseleave:B,onClick:D},[e("span",{class:"v-rating__hidden"},[_(a.itemAriaLabel,c,a.length)]),v?i.item?i.item({...U.value[m],props:G,value:c,index:m,rating:r.value}):e(f,he({"aria-label":_(a.itemAriaLabel,c,a.length)},G),null):void 0]),e("input",{class:"v-rating__hidden",name:Y.value,id:P,type:"radio",value:c,checked:r.value===c,tabindex:-1,readonly:a.readonly,disabled:a.disabled},null)])}function J(d){return i["item-label"]?i["item-label"](d):d.label?e("span",null,[d.label]):e("span",null,[s(" ")])}return xe(()=>{var c;const d=!!((c=a.itemLabels)!=null&&c.length)||i["item-label"];return e(a.tag,{class:["v-rating",{"v-rating--hover":a.hover,"v-rating--readonly":a.readonly},t.value,a.class],style:a.style},{default:()=>[e($,{value:0,index:-1,showStar:!1},null),W.value.map((m,v)=>{var L,B;return e("div",{class:"v-rating__wrapper"},[d&&a.itemLabelPosition==="top"?J({value:m,index:v,label:(L=a.itemLabels)==null?void 0:L[v]}):void 0,e("div",{class:"v-rating__item"},[a.halfIncrements?e(C,null,[e($,{value:m-.5,index:v*2},null),e($,{value:m,index:v*2+1},null)]):e($,{value:m,index:v},null)]),d&&a.itemLabelPosition==="bottom"?J({value:m,index:v,label:(B=a.itemLabels)==null?void 0:B[v]}):void 0])})]})}),{}}}),ze={class:"d-flex justify-space-between flex-wrap pt-8"},Ue={class:"me-2 mb-2"},De={class:"d-flex justify-space-between align-center mt-8"},He={class:"v-avatar-group"},Oe={class:"d-flex justify-space-between flex-wrap flex-md-nowrap flex-column flex-md-row"},We={class:"ma-auto pa-5"},qe={class:"d-flex flex-column-reverse flex-md-row"},Ye={class:"ma-auto pa-5"},Je={class:"me-auto pe-4"},Ge={class:"d-flex align-center mb-6"},Ke={class:"d-flex align-center mb-0"},Xe={class:"ms-auto ps-4"},Ze={class:"d-flex align-center mb-6"},Qe={class:"d-flex align-center mb-0"},el={class:"membership-pricing d-flex flex-column align-center py-14 h-100 justify-center"},ll={__name:"CardBasic",setup(a){const b=[O,Le,Me,se],i=H(!1);return(_,t)=>(w(),j(z,null,{default:l(()=>[e(g,{cols:"12",sm:"6",md:"4"},{default:l(()=>[e(y,null,{default:l(()=>[e(A,{src:p($e),cover:""},null,8,["src"]),e(h,null,{default:l(()=>[e(x,null,{default:l(()=>t[2]||(t[2]=[s("Influencing The Influencer")])),_:1})]),_:1}),e(u,null,{default:l(()=>t[3]||(t[3]=[s(" Cancun is back, better than ever! Over a hundred Mexico resorts have reopened and the state tourism minister predicts Cancun will draw as many visitors in 2006 as it did two years ago. ")])),_:1})]),_:1})]),_:1}),e(g,{cols:"12",sm:"6",md:"4"},{default:l(()=>[e(y,null,{default:l(()=>[e(A,{src:p(Pe)},null,8,["src"]),e(u,{class:"position-relative"},{default:l(()=>[e(R,{size:"75",class:"avatar-center",image:p(O)},null,8,["image"]),n("div",ze,[n("div",Ue,[e(x,{class:"pa-0"},{default:l(()=>t[4]||(t[4]=[s(" Robert Meyer ")])),_:1}),e(Ae,{class:"text-caption pa-0"},{default:l(()=>t[5]||(t[5]=[s(" London, UK ")])),_:1})]),e(f,null,{default:l(()=>t[6]||(t[6]=[s("send request")])),_:1})]),n("div",De,[t[7]||(t[7]=n("span",{class:"font-weight-medium"},"18 mutual friends",-1)),n("div",He,[(w(),I(C,null,S(b,o=>e(R,{key:o,image:o,size:"40"},null,8,["image"])),64))])])]),_:1})]),_:1})]),_:1}),e(g,{cols:"12",md:"4",sm:"6"},{default:l(()=>[e(y,null,{default:l(()=>[e(A,{src:p(Fe)},null,8,["src"]),e(h,null,{default:l(()=>[e(x,null,{default:l(()=>t[8]||(t[8]=[s("Popular Uses Of The Internet")])),_:1})]),_:1}),e(u,null,{default:l(()=>t[9]||(t[9]=[s(" Although cards can support multiple actions, UI controls, and an overflow menu. ")])),_:1}),e(M,null,{default:l(()=>[e(f,{onClick:t[0]||(t[0]=o=>i.value=!p(i))},{default:l(()=>t[10]||(t[10]=[s(" Details ")])),_:1}),e(Se),e(f,{icon:"",size:"small",onClick:t[1]||(t[1]=o=>i.value=!p(i))},{default:l(()=>[e(V,{icon:p(i)?"ri-arrow-up-s-line":"ri-arrow-down-s-line"},null,8,["icon"])]),_:1})]),_:1}),e(Te,null,{default:l(()=>[Ve(n("div",null,[e(E),e(u,null,{default:l(()=>t[11]||(t[11]=[s(" I'm a thing. But, like most politicians, he promised more than he could deliver. You won't have time for sleeping, soldier, not with all the bed making you'll be doing. Then we'll go with that data file! Hey, you add a one and two zeros to that or we walk! You're going to do his laundry? I've got to find a way to escape. ")])),_:1})],512),[[Ce,p(i)]])]),_:1})]),_:1})]),_:1}),e(g,{sm:"6",cols:"12"},{default:l(()=>[e(y,null,{default:l(()=>[n("div",Oe,[n("div",We,[e(A,{width:"137",height:"176",src:p(Ne)},null,8,["src"])]),e(E,{vertical:_.$vuetify.display.mdAndUp},null,8,["vertical"]),n("div",null,[e(h,null,{default:l(()=>[e(x,null,{default:l(()=>t[12]||(t[12]=[s("Apple iPhone 11 Pro")])),_:1})]),_:1}),e(u,null,{default:l(()=>t[13]||(t[13]=[s(" Apple iPhone 11 Pro smartphone. Announced Sep 2019. Features 5.8″ display Apple A13 Bionic ")])),_:1}),e(u,{class:"text-subtitle-1"},{default:l(()=>t[14]||(t[14]=[n("span",null,"Price :",-1),s(),n("span",{class:"font-weight-medium"},"$899",-1)])),_:1}),e(M,{class:"justify-space-between"},{default:l(()=>[e(f,null,{default:l(()=>[e(V,{icon:"ri-shopping-cart-line"}),t[15]||(t[15]=n("span",{class:"ms-2"},"Add to cart",-1))]),_:1}),e(f,{color:"secondary",icon:"ri-share-line"})]),_:1})])])]),_:1})]),_:1}),e(g,{sm:"6",cols:"12"},{default:l(()=>[e(y,null,{default:l(()=>[n("div",qe,[n("div",null,[e(h,null,{default:l(()=>[e(x,null,{default:l(()=>t[16]||(t[16]=[s("Stumptown Roasters")])),_:1})]),_:1}),e(u,{class:"d-flex align-center flex-wrap text-body-1"},{default:l(()=>[e(ae,{"model-value":5,readonly:"",class:"me-3",density:"compact"}),t[17]||(t[17]=n("span",null,"5 Star | 98 reviews",-1))]),_:1}),e(u,null,{default:l(()=>t[18]||(t[18]=[s(" Before there was a United States of America, there were coffee houses, because how are you supposed to build. ")])),_:1}),e(M,null,{default:l(()=>[e(f,null,{default:l(()=>t[19]||(t[19]=[s("Location")])),_:1}),e(f,null,{default:l(()=>t[20]||(t[20]=[s("Reviews")])),_:1})]),_:1})]),n("div",Ye,[e(A,{width:176,src:p(Ee),class:"rounded"},null,8,["src"])])])]),_:1})]),_:1}),e(g,{lg:"4",sm:"6",cols:"12"},{default:l(()=>[e(y,null,{default:l(()=>[e(A,{src:p(Re)},null,8,["src"]),e(h,null,{default:l(()=>[e(x,null,{default:l(()=>t[21]||(t[21]=[s("Apple Watch")])),_:1})]),_:1}),e(u,null,{default:l(()=>t[22]||(t[22]=[n("p",{class:"font-weight-medium text-base"}," $249.40 ",-1),n("p",{class:"mb-0"}," 3.1GHz 6-core 10th-generation Intel Core i5 processor, Turbo Boost up to 4.5GHz ",-1)])),_:1}),e(f,{block:"",class:"rounded-t-0"},{default:l(()=>t[23]||(t[23]=[s(" Add to cart ")])),_:1})]),_:1})]),_:1}),e(g,{md:"6",lg:"8",cols:"12"},{default:l(()=>[e(y,null,{default:l(()=>[e(z,{"no-gutters":""},{default:l(()=>[e(g,{cols:"12",sm:"8",md:"12",lg:"7",order:"2","order-lg":"1"},{default:l(()=>[e(h,null,{default:l(()=>[e(x,null,{default:l(()=>t[24]||(t[24]=[s("Lifetime Membership")])),_:1})]),_:1}),e(u,null,{default:l(()=>t[25]||(t[25]=[s(" Here, I focus on a range of items and features that we use in life without giving them a second thought such as Coca Cola, body muscles and holding ones own breath. Though, most of these notes are not fundamentally necessary, they are such that you can use them for a good laugh, at a drinks party or for picking up women or men. ")])),_:1}),e(u,null,{default:l(()=>[e(E)]),_:1}),e(u,{class:"d-flex justify-center"},{default:l(()=>[n("div",Je,[n("p",Ge,[e(V,{color:"primary",icon:"ri-lock-unlock-line"}),t[26]||(t[26]=n("span",{class:"ms-3"},"Full Access",-1))]),n("p",Ke,[e(V,{color:"primary",icon:"ri-user-line"}),t[27]||(t[27]=n("span",{class:"ms-3"},"15 Members",-1))])]),_.$vuetify.display.smAndUp?(w(),j(E,{key:0,vertical:"",inset:""})):Ie("",!0),n("div",Xe,[n("p",Ze,[e(V,{color:"primary",icon:"ri-star-line"}),t[28]||(t[28]=n("span",{class:"ms-3"},"Access all Features",-1))]),n("p",Qe,[e(V,{color:"primary",icon:"ri-pie-chart-2-line"}),t[29]||(t[29]=n("span",{class:"ms-3"},"Lifetime Free Update",-1))])])]),_:1})]),_:1}),e(g,{cols:"12",sm:"4",md:"12",lg:"5",order:"1","order-lg":"2",class:"member-pricing-bg text-center"},{default:l(()=>[n("div",el,[t[31]||(t[31]=n("p",{class:"mb-5"},[n("sub",{class:"text-h5"},"$"),n("sup",{class:"text-h2 font-weight-medium"},"899"),n("sub",{class:"text-h5"},"USD")],-1)),t[32]||(t[32]=n("p",{class:"text-sm"},[s(" 5 Tips For Offshore "),n("br"),s(" Software Development ")],-1)),e(f,{class:"mt-8"},{default:l(()=>t[30]||(t[30]=[s(" Contact Now ")])),_:1})])]),_:1})]),_:1})]),_:1})]),_:1}),e(g,{cols:"12",lg:"4",md:"6"},{default:l(()=>[e(y,{title:"Influencing The Influencer"},{default:l(()=>[e(u,null,{default:l(()=>t[33]||(t[33]=[s(" Computers have become ubiquitous in almost every facet of our lives. At work, desk jockeys spend hours in front of their desktops, while delivery people scan bar codes with handhelds and workers in the field stay in touch. ")])),_:1}),e(u,null,{default:l(()=>t[34]||(t[34]=[s(" If you're in the market for new desktops, notebooks, or PDAs, there are a myriad of choices. Here's a rundown of some of the best systems available. ")])),_:1}),e(M,null,{default:l(()=>[e(f,null,{default:l(()=>t[35]||(t[35]=[s("Read More")])),_:1})]),_:1})]),_:1})]),_:1}),e(g,{cols:"12",lg:"4",md:"6"},{default:l(()=>[e(y,{title:"The Best Answers"},{default:l(()=>[e(u,{class:"d-flex align-center flex-wrap"},{default:l(()=>[e(ae,{"model-value":5,readonly:"",density:"compact",class:"me-3"}),t[36]||(t[36]=n("span",{class:"text-subtitle-2"},"5 Star | 98 reviews",-1))]),_:1}),e(u,null,{default:l(()=>t[37]||(t[37]=[s(" If you are looking for a new way to promote your business that won't cost you more money, maybe printing is one of the options you won't resist. ")])),_:1}),e(u,null,{default:l(()=>t[38]||(t[38]=[s(" become fast, easy and simple. If you want your promotional material to be an eye-catching ")])),_:1}),e(M,null,{default:l(()=>[e(f,null,{default:l(()=>t[39]||(t[39]=[s("Location")])),_:1}),e(f,null,{default:l(()=>t[40]||(t[40]=[s("Reviews")])),_:1})]),_:1})]),_:1})]),_:1}),e(g,{cols:"12",md:"6",lg:"4"},{default:l(()=>[e(y,{class:"text-center"},{default:l(()=>[e(u,{class:"d-flex flex-column justify-center align-center"},{default:l(()=>[e(R,{color:"primary",variant:"outlined",size:"50",class:"mb-4"},{default:l(()=>[e(V,{size:"2rem",icon:"ri-question-line"})]),_:1}),t[41]||(t[41]=n("h6",{class:"text-h6"}," Support ",-1))]),_:1}),e(u,null,{default:l(()=>t[42]||(t[42]=[n("p",null," According to us blisters are a very common thing and we come across them very often in our daily lives. It is a very common occurrence like cold or fever depending upon your lifestyle. ",-1)])),_:1}),e(u,{class:"justify-center"},{default:l(()=>[e(f,{variant:"elevated"},{default:l(()=>t[43]||(t[43]=[s(" Contact Now ")])),_:1})]),_:1})]),_:1})]),_:1})]),_:1}))}},tl=ke(ll,[["__scopeId","data-v-5f9dcacd"]]),ne="Although cards can support multiple actions, UI controls, and an overflow menu, use restraint and remember that cards...",al={__name:"CardNavigation",setup(a){const b=H("ITEM ONE"),i=H("ITEM ONE"),_=["ITEM ONE","ITEM TWO","ITEM THREE"];return(t,o)=>(w(),j(z,null,{default:l(()=>[e(g,{md:"6",cols:"12"},{default:l(()=>[e(y,null,{default:l(()=>[e(Q,{modelValue:p(b),"onUpdate:modelValue":o[0]||(o[0]=r=>F(b)?b.value=r:null)},{default:l(()=>[(w(),I(C,null,S(_,r=>e(ee,{key:r,value:r},{default:l(()=>[s(k(r),1)]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"]),e(le,{modelValue:p(b),"onUpdate:modelValue":o[1]||(o[1]=r=>F(b)?b.value=r:null)},{default:l(()=>[(w(),I(C,null,S(_,r=>e(te,{key:r,value:r},{default:l(()=>[e(h,null,{default:l(()=>[e(x,null,{default:l(()=>o[4]||(o[4]=[s("Navigation Card")])),_:1})]),_:1}),e(u,null,{default:l(()=>[s(k(ne))]),_:1}),e(u,null,{default:l(()=>[e(f,null,{default:l(()=>o[5]||(o[5]=[s("Learn More")])),_:1})]),_:1})]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"])]),_:1})]),_:1}),e(g,{md:"6",cols:"12"},{default:l(()=>[e(y,null,{default:l(()=>[e(Q,{modelValue:p(i),"onUpdate:modelValue":o[2]||(o[2]=r=>F(i)?i.value=r:null),"align-tabs":"center"},{default:l(()=>[(w(),I(C,null,S(_,r=>e(ee,{key:r,value:r},{default:l(()=>[s(k(r),1)]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"]),e(le,{modelValue:p(i),"onUpdate:modelValue":o[3]||(o[3]=r=>F(i)?i.value=r:null)},{default:l(()=>[(w(),I(C,null,S(_,r=>e(te,{key:r,value:r,class:"text-center"},{default:l(()=>[e(h,null,{default:l(()=>[e(x,null,{default:l(()=>o[6]||(o[6]=[s("Navigation Card")])),_:1})]),_:1}),e(u,null,{default:l(()=>[s(k(ne))]),_:1}),e(u,null,{default:l(()=>[e(f,null,{default:l(()=>o[7]||(o[7]=[s("Learn More")])),_:1})]),_:1})]),_:2},1032,["value"])),64))]),_:1},8,["modelValue"])]),_:1})]),_:1})]),_:1}))}},nl="/build/assets/avatar-8-DkWluxoH.png",sl={class:"clamp-text text-white mb-0"},ol={class:"text-no-wrap"},il={class:"text-white ms-2"},rl={class:"d-flex align-center"},ul={class:"text-subtitle-2 text-white me-4"},dl={class:"text-subtitle-2 text-white"},ml={__name:"CardSolid",setup(a){const b=[{cardBg:"#16B1FF",title:"Twitter Card",icon:"ri-twitter-line",text:'"Turns out semicolon-less style is easier and safer in TS because most gotcha edge cases are type invalid as well."',avatarImg:se,avatarName:"Mary Vaughn",likes:"1.2k",share:"80"},{cardBg:"#3B5998",title:"Facebook Card",icon:"ri-facebook-circle-line",text:"You've read about the importance of being courageous, rebellious and imaginative. These are all vital ingredients.",avatarImg:O,avatarName:"Eugene Clarke",likes:"3.2k",share:"49"},{cardBg:"#007BB6",title:"LinkedIn Card",icon:"ri-linkedin-box-line",text:"With the Internet spreading like wildfire and reaching every part of our daily life, more and more traffic is directed.",avatarImg:nl,avatarName:"Anne Burke1",likes:"1.2k",share:"80"}];return(i,_)=>{const t=Be("IconBtn");return w(),j(z,null,{default:l(()=>[(w(),I(C,null,S(b,o=>e(g,{key:o.icon,cols:"12",md:"6",lg:"4"},{default:l(()=>[e(y,{color:o.cardBg},{default:l(()=>[e(h,null,{prepend:l(()=>[e(V,{size:"1.9rem",color:"white",icon:o.icon},null,8,["icon"])]),default:l(()=>[e(x,{class:"text-white"},{default:l(()=>[s(k(o.title),1)]),_:2},1024)]),_:2},1024),e(u,null,{default:l(()=>[n("p",sl,k(o.text),1)]),_:2},1024),e(u,{class:"d-flex justify-space-between align-center flex-wrap"},{default:l(()=>[n("div",ol,[e(R,{size:"34",image:o.avatarImg},null,8,["image"]),n("span",il,k(o.avatarName),1)]),n("div",rl,[e(t,{icon:"ri-heart-line",color:"white",class:"me-1"}),n("span",ul,k(o.likes),1),e(t,{icon:"ri-share-line",color:"white",class:"me-1"}),n("span",dl,k(o.share),1)])]),_:2},1024)]),_:2},1032,["color"])]),_:2},1024)),64))]),_:1})}}},Cl={__name:"cards",setup(a){return(b,i)=>(w(),I("div",null,[i[0]||(i[0]=n("p",{class:"text-2xl mb-6"}," Basic Cards ",-1)),e(tl),i[1]||(i[1]=n("p",{class:"text-2xl mb-6 mt-14"}," Navigation Cards ",-1)),e(al),i[2]||(i[2]=n("p",{class:"text-2xl mt-14 mb-6"}," Solid Cards ",-1)),e(ml)]))}};export{Cl as default};
