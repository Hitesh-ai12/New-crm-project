import{_ as u,o as s,l as o,k as a,Y as r,d as _,b as p,h as t,j as h,u as c,g,$ as f,O as k}from"./main-CK36iqPX.js";import{V as n}from"./VImg-Djd7BO5H.js";const b={class:"text-center mb-4"},x={key:0,class:"header-title font-weight-medium"},y={key:1,class:"text-h5 font-weight-medium mb-3"},v={key:2},w={__name:"ErrorHeader",props:{statusCode:{type:[String,Number],required:!1},title:{type:String,required:!1},description:{type:String,required:!1}},setup(i){const e=i;return(d,l)=>(s(),o("div",b,[e.statusCode?(s(),o("h1",x,a(e.statusCode),1)):r("",!0),e.title?(s(),o("h5",y,a(e.title),1)):r("",!0),e.description?(s(),o("p",v,a(e.description),1)):r("",!0)]))}},V=u(w,[["__scopeId","data-v-7fa56302"]]),C="/build/assets/404-KybqypYR.png",N="/build/assets/misc-mask-dark-EDTHQMEO.png",S="/build/assets/misc-mask-light-PdlO0S62.png",B="/build/assets/tree-3HmZpwoD.png",E={class:"misc-wrapper"},H={class:"misc-avatar w-100 text-center"},M={__name:"[...error]",setup(i){const e=_(),d=p(()=>e.global.name.value==="light"?S:N);return(l,T)=>{const m=V;return s(),o("div",E,[t(m,{"status-code":"404",title:"Page Not Found ⚠️",description:"We couldn't find the page you are looking for."}),h("div",H,[t(n,{src:c(C),alt:"Coming Soon","max-width":800,class:"mx-auto"},null,8,["src"]),t(f,{to:"/",class:"mt-10"},{default:g(()=>[k(" Back to Home ")]),_:1})]),t(n,{src:c(B),class:"misc-footer-tree d-none d-md-block"},null,8,["src"]),t(n,{src:c(d),class:"misc-footer-img d-none d-md-block"},null,8,["src"])])}}};export{M as default};