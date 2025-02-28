import{a as m}from"./axios-CdtiCRuU.js";import{S as n}from"./sweetalert2.all-B3MH3p00.js";import{_ as F,r as c,b as U,l as f,j as e,L as h,bn as y,F as V,Y as z,k as p,aG as N,X as Y,o as v,bp as L}from"./main-Dr3_htUk.js";import"./_commonjsHelpers-Cpj98o6Y.js";const Q={setup(){const u=c(!1),t=c(!1),a=c({type:"sms",title:"",content:"",id:null}),s=c([]),d=c([]),b=c(""),o=()=>localStorage.getItem("auth_token"),i=async()=>{const l=o();try{const r=await m.get("/api/templates",{params:{type:"sms"},headers:{Authorization:`Bearer ${l}`}});s.value=r.data.data}catch(r){console.error("Error fetching templates:",r)}},g=U(()=>s.value.filter(l=>l.title.toLowerCase().includes(b.value.toLowerCase()))),C=()=>{u.value=!0,t.value=!1,T()},S=l=>{a.value={...l},u.value=!0,t.value=!0},k=()=>{u.value=!1,T()},T=()=>{a.value.title="",a.value.content="",a.value.id=null},x=async()=>{try{t.value?(await B(),n.fire("Updated!","The template has been updated successfully.","success")):(await A(),n.fire("Created!","The template has been created successfully.","success")),k(),i()}catch(l){console.error("Error submitting form:",l),n.fire("Error!","There was an issue saving the template.","error")}},A=async()=>{const l=o();if(!l){console.error("Auth token is missing");return}try{await m.post("/api/templates",a.value,{headers:{Authorization:`Bearer ${l}`}})}catch(r){console.error("Error creating template:",r)}},B=async()=>{const l=o();if(!l){console.error("Auth token is missing");return}try{await m.put(`/api/templates/${a.value.id}`,a.value,{headers:{Authorization:`Bearer ${l}`}})}catch(r){console.error("Error updating template:",r)}},E=async l=>{const r=o();if(!r){console.error("Auth token is missing");return}if((await n.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonColor:"#d33",cancelButtonColor:"#3085d6",confirmButtonText:"Yes, delete it!"})).isConfirmed)try{await m.delete(`/api/templates/${l}`,{headers:{Authorization:`Bearer ${r}`}}),n.fire("Deleted!","The template has been deleted.","success"),i()}catch(D){console.error("Error deleting template:",D),n.fire("Error!","There was an issue deleting the template.","error")}},_=async()=>{const l=o();if(!l){console.error("Auth token is missing");return}if((await n.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonColor:"#d33",cancelButtonColor:"#3085d6",confirmButtonText:"Yes, delete them!"})).isConfirmed)try{await m.delete("/api/templates",{data:{ids:d.value},headers:{Authorization:`Bearer ${l}`}}),n.fire("Deleted!","The selected templates have been deleted.","success"),i()}catch(w){console.error("Error deleting selected templates:",w),n.fire("Error!","There was an issue deleting the selected templates.","error")}},M=()=>{d.value.length===g.value.length?d.value=[]:d.value=g.value.map(l=>l.id)};return i(),{isModalOpen:u,isEditing:t,formData:a,smsTemplates:s,selectedTemplates:d,searchQuery:b,filteredTemplates:g,openModal:C,closeModal:k,submitForm:x,resetForm:T,editTemplate:S,deleteTemplate:E,deleteSelectedTemplates:_,toggleSelectAll:M}}},q={class:"main"},O={class:"main-2"},j={class:"heading-section"},G={class:"sms-icon"},I={class:"search"},R={class:"data-section"},X={class:"table-sms-body"},H={class:"sms_checkbox_field"},J={class:"sms_checkbox_field"},K=["value"],P=["onClick"],W=["onClick"],Z={key:0,class:"modal-overlay"},$={class:"modal"},ee={class:"form-group"},te={class:"form-group"},oe={type:"submit",class:"submit-btn"};function se(u,t,a,s,d,b){return v(),f("div",q,[e("div",O,[e("div",j,[t[9]||(t[9]=e("div",{class:"sms-templates"},[e("h3",null,"SMS Template")],-1)),e("div",G,[e("i",{class:"fa-regular fa-plus",onClick:t[0]||(t[0]=(...o)=>s.openModal&&s.openModal(...o))}),e("i",{class:"fa-solid fa-trash-can-arrow-up",onClick:t[1]||(t[1]=(...o)=>s.deleteSelectedTemplates&&s.deleteSelectedTemplates(...o))})])]),e("div",I,[h(e("input",{type:"text","onUpdate:modelValue":t[2]||(t[2]=o=>s.searchQuery=o),placeholder:"     ENTER SMS template NAME"},null,512),[[y,s.searchQuery]])]),e("div",R,[e("table",X,[e("thead",null,[e("tr",null,[e("th",H,[e("input",{type:"checkbox",class:"sms_checkbox",onChange:t[3]||(t[3]=(...o)=>s.toggleSelectAll&&s.toggleSelectAll(...o))},null,32)]),t[10]||(t[10]=e("th",null,"Name",-1)),t[11]||(t[11]=e("th",null,"Created By",-1)),t[12]||(t[12]=e("th",null,"Body",-1)),t[13]||(t[13]=e("th",null,"Action",-1))])]),e("tbody",null,[(v(!0),f(V,null,z(s.filteredTemplates,o=>(v(),f("tr",{key:o.id},[e("td",J,[h(e("input",{type:"checkbox","onUpdate:modelValue":t[4]||(t[4]=i=>s.selectedTemplates=i),value:o.id},null,8,K),[[L,s.selectedTemplates]])]),e("td",null,p(o.title),1),e("td",null,p(o.created_by),1),e("td",null,p(o.content),1),e("td",null,[e("button",{onClick:i=>s.editTemplate(o)},"Edit",8,P),e("button",{onClick:i=>s.deleteTemplate(o.id)},"Delete",8,W)])]))),128))])])]),s.isModalOpen?(v(),f("div",Z,[e("div",$,[e("button",{class:"close-btn",onClick:t[5]||(t[5]=(...o)=>s.closeModal&&s.closeModal(...o))},"×"),e("h3",null,p(s.isEditing?"Edit SMS Template":"Add SMS Template"),1),e("form",{onSubmit:t[8]||(t[8]=N((...o)=>s.submitForm&&s.submitForm(...o),["prevent"]))},[e("div",ee,[t[14]||(t[14]=e("label",{for:"title"},"Title",-1)),h(e("input",{type:"text",id:"title","onUpdate:modelValue":t[6]||(t[6]=o=>s.formData.title=o),required:""},null,512),[[y,s.formData.title]])]),e("div",te,[t[15]||(t[15]=e("label",{for:"content"},"Content",-1)),h(e("textarea",{id:"content","onUpdate:modelValue":t[7]||(t[7]=o=>s.formData.content=o),rows:"4",required:""},null,512),[[y,s.formData.content]])]),e("button",oe,p(s.isEditing?"Update":"Submit"),1)],32)])])):Y("",!0)])])}const ie=F(Q,[["render",se]]);export{ie as default};
