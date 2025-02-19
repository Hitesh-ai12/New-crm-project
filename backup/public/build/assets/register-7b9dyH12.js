import{r as d,d as x,b as O,l as w,h as e,g as t,u as m,o as p,j as o,aF as _,f as v,W as V,aE as C,O as E,k as F,e as M}from"./main-B6lduhDs.js";import{l as B}from"./logo-DbYPuVAr.js";import{a as L,b as N}from"./auth-v1-mask-light--N6hHkxK.js";import{a as c}from"./axios-DsPaXkF5.js";import{a as R,b as S,V as h}from"./VCard-DAWIigei.js";import{V as f}from"./VImg-CuSWVABe.js";import{a as j,V as y}from"./VTextField-A31ccmRO.js";import{V as H,a as i}from"./VRow-C5YPH_aT.js";import"./VAvatar-DCRWcC4u.js";import"./forwardRefs-DWGaNmQL.js";import"./index-B8zzSXAV.js";/* empty css              */const I="/build/assets/auth-v1-tree-2-Cg13PFRH.png",q="/build/assets/auth-v1-tree-XwsB2ezL.png",D={class:"auth-wrapper d-flex align-center justify-center pa-4"},U=["innerHTML"],te={__name:"register",setup(z){const s=d({email:"",otp:""}),T=x(),b=O(()=>T.global.name.value==="light"?L:N),r=d(!1),u=d(!1),g=async()=>{try{(await c.post("/api/send-otp",{email:s.value.email})).data.success?(r.value=!0,alert("OTP sent to your email!")):alert("Failed to send OTP. Please try again.")}catch(l){console.error("Error sending OTP:",l),alert("Failed to send OTP. Please try again.")}},P=async()=>{try{(await c.post("/api/verify-otp",{email:s.value.email,otp:s.value.otp})).data.success?(u.value=!0,await c.post("/api/send-password",{email:s.value.email}),alert("Password has been sent to your email!")):alert("Invalid OTP. Please try again.")}catch(l){console.error("Error verifying OTP:",l),alert("Failed to verify OTP. Please try again.")}};return(l,a)=>{const k=M("RouterLink");return p(),w("div",D,[e(R,{class:"auth-card pa-4 pt-7","max-width":"448"},{default:t(()=>[e(S,{class:"justify-center"},{default:t(()=>[e(k,{to:"/",class:"d-flex align-center gap-3"},{default:t(()=>[o("div",{class:"d-flex",innerHTML:m(B)},null,8,U),a[3]||(a[3]=o("h2",{class:"font-weight-medium text-2xl text-uppercase"}," Materio ",-1))]),_:1})]),_:1}),e(h,{class:"pt-2"},{default:t(()=>a[4]||(a[4]=[o("h4",{class:"text-h4 mb-1"},"Adventure starts here 🚀",-1),o("p",{class:"mb-0"},"Enter your email to receive an OTP!",-1)])),_:1}),e(h,null,{default:t(()=>[e(j,{onSubmit:a[2]||(a[2]=_(n=>r.value?P():g(),["prevent"]))},{default:t(()=>[e(H,null,{default:t(()=>[e(i,{cols:"12"},{default:t(()=>[e(y,{modelValue:s.value.email,"onUpdate:modelValue":a[0]||(a[0]=n=>s.value.email=n),label:"Email",type:"email",disabled:r.value,required:""},null,8,["modelValue","disabled"])]),_:1}),r.value&&!u.value?(p(),v(i,{key:0,cols:"12"},{default:t(()=>[e(y,{modelValue:s.value.otp,"onUpdate:modelValue":a[1]||(a[1]=n=>s.value.otp=n),label:"OTP",placeholder:"Enter the OTP",required:""},null,8,["modelValue"])]),_:1})):V("",!0),e(i,{cols:"12"},{default:t(()=>[e(C,{block:"",type:"submit"},{default:t(()=>[E(F(r.value?"Verify OTP":"Send OTP"),1)]),_:1})]),_:1}),u.value?(p(),v(i,{key:1,cols:"12",class:"text-center text-base"},{default:t(()=>a[5]||(a[5]=[o("span",null,"Password has been sent to your email!",-1)])),_:1})):V("",!0)]),_:1})]),_:1})]),_:1})]),_:1}),e(f,{class:"auth-footer-start-tree d-none d-md-block",src:m(q),width:250},null,8,["src"]),e(f,{src:m(I),class:"auth-footer-end-tree d-none d-md-block",width:350},null,8,["src"]),e(f,{class:"auth-footer-mask d-none d-md-block",src:b.value},null,8,["src"])])}}};export{te as default};
