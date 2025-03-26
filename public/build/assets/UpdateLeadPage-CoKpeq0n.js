import{a as y}from"./axios-CdtiCRuU.js";import{_ as U,l as u,j as s,k as p,P as f,bP as k,n as r,F as T,Y as V,X as m,aG as C,L as o,d6 as n,d3 as b,o as d}from"./main-DEwZK6_v.js";const I={data(){return{activeTab:"timeline",activeMenuItem:"all",leadId:null,contact:{first_name:"",last_name:"",email:"",phone:"",stage:"",source:"",customTag:""},user:{name:"",email:"",phone:"",stage:"",tags:"",city:"",background:"",house_number:"",street:"",province:"",zip_code:""}}},methods:{setActiveTab(i){this.activeTab=i},async fetchLead(i){var e;console.log("Fetching lead for ID:",i);try{const a=localStorage.getItem("auth_token");if(!a)throw new Error("No authentication token found.");const t=(await y.get(`http://127.0.0.1:8000/api/leads/${i}`,{headers:{Authorization:`Bearer ${a}`,Accept:"application/json"}})).data;if(!t||!t.id)throw new Error("Invalid lead data received");this.contact={first_name:t.first_name||"",last_name:t.last_name||"",email:t.email||"",phone:t.phone||"",stage:t.stage||"",source:t.source||"",customTag:t.tag||""},this.user={name:`${t.first_name||""} ${t.last_name||""}`.trim(),email:t.email||"",phone:t.phone||"",stage:t.stage||"",tags:t.tag||"",city:t.city||"",background:t.background||"",house_number:t.house_number||"",street:t.street||"",province:t.province||"",zip_code:t.zip_code||""},this.leadId=t.id}catch(a){console.error("Error fetching lead:",((e=a.response)==null?void 0:e.data)||a.message)}},async updateLead(){var i;if(!this.leadId){alert("Lead ID is missing!");return}try{const e=localStorage.getItem("auth_token");if(!e)throw new Error("No authentication token found.");const a=this.user.name.trim().split(" "),g=a[0]||"",t=a.slice(1).join(" ")||"",v=await y.put(`http://127.0.0.1:8000/api/leads/${this.leadId}`,{first_name:g,last_name:t,email:this.user.email,phone:this.user.phone,stage:this.user.stage,tag:this.user.tags,city:this.user.city,background:this.user.background,house_number:this.user.house_number,street:this.user.street,province:this.user.province,zip_code:this.user.zip_code},{headers:{Authorization:`Bearer ${e}`,Accept:"application/json"}});console.log("Lead updated successfully:",v.data),alert("Lead updated successfully!")}catch(e){console.error("Update failed:",((i=e.response)==null?void 0:i.data)||e.message),alert("Failed to update lead.")}}},mounted(){const i=this.$route.params.id;i?this.fetchLead(i):console.error("Lead ID is missing in route!")}},L={class:"lead-management"},w={class:"header"},M={class:"contact-info"},S={class:"avatar"},x={class:"details"},N={class:"tags"},P={class:"tag stage"},E={class:"tag source"},z={class:"tag custom"},D={class:"navigation"},q={key:0,class:"container"},B={class:"sidebar"},F={class:"main-content"},j={class:"activities"},H={class:"activity-details"},O={class:"activity-time"},R={key:1,class:"container"},W={class:"main-content"},G={class:"form-grid"},X={class:"form-group"},Y={class:"form-group"},Z={class:"form-group"},J={class:"form-group"},K={class:"form-group"},Q={class:"form-group"},c={class:"form-group"},h={class:"form-group"},_={class:"form-group"},$={class:"form-group"},ee={class:"form-group"},se={class:"form-group"},te={class:"form-group"},le={class:"form-group"},oe={class:"form-group"},ne={class:"form-group"},ie={class:"form-group"},re={class:"form-group"},ue={class:"form-group"},de={class:"form-group"},ae={class:"form-group"},pe={class:"form-group"},me={class:"form-group"},ve={class:"form-group"},be={class:"form-group"},ge={class:"form-group"},fe={class:"form-group"},ye={class:"form-group"},ke={class:"form-group"},Ae={class:"form-group"},Ue={class:"form-group"},Te={class:"form-group"},Ve={class:"form-group"},Ce={class:"form-group"},Ie={key:2,class:"analytics-container"},Le={class:"sidebar"},we={class:"main-content"},Me={class:"analytics-content"},Se={key:0},xe={key:1},Ne={key:2},Pe={key:3},Ee={key:4};function ze(i,e,a,g,t,v){return d(),u("div",L,[s("div",w,[s("div",M,[s("div",S,p(i.firstLetter),1),s("div",x,[s("h2",null,p(t.contact.first_name),1),s("div",N,[s("span",P,[e[51]||(e[51]=s("i",{class:"fas fa-chart-line"},null,-1)),f(" "+p(t.contact.stage),1)]),s("span",E,[e[52]||(e[52]=s("i",{class:"fas fa-globe"},null,-1)),f(" "+p(t.contact.source),1)]),s("span",z,[e[53]||(e[53]=s("i",{class:"fas fa-tag"},null,-1)),f(" "+p(t.contact.customTag),1)])])])]),e[54]||(e[54]=k('<div class="actions" data-v-9a97e57d><button class="btn danger" data-v-9a97e57d><i class="fas fa-microphone" data-v-9a97e57d></i></button><button class="btn danger" data-v-9a97e57d><i class="fas fa-times" data-v-9a97e57d></i></button></div>',1))]),s("div",D,[s("button",{class:r(["nav-btn",t.activeTab==="timeline"?"active":""]),onClick:e[0]||(e[0]=l=>t.activeTab="timeline")},"Timeline",2),s("button",{class:r(["nav-btn",t.activeTab==="profile"?"active":""]),onClick:e[1]||(e[1]=l=>t.activeTab="profile")},"Profile",2),s("button",{class:r(["nav-btn",t.activeTab==="analytics"?"active":""]),onClick:e[2]||(e[2]=l=>t.activeTab="analytics")},"Analytics",2)]),t.activeTab==="timeline"?(d(),u("div",q,[s("div",B,[s("div",{class:r(["menu-item",{active:t.activeMenuItem==="all"}]),onClick:e[3]||(e[3]=l=>t.activeMenuItem="all")},e[55]||(e[55]=[s("div",{class:"icon"},[s("i",{class:"fas fa-headset"})],-1),s("div",{class:"label"},"ALL",-1)]),2),s("div",{class:r(["menu-item",{active:t.activeMenuItem==="sms"}]),onClick:e[4]||(e[4]=l=>t.activeMenuItem="sms")},e[56]||(e[56]=[s("div",{class:"icon"},[s("i",{class:"fas fa-comment"})],-1),s("div",{class:"label"},"SMS",-1)]),2),s("div",{class:r(["menu-item",{active:t.activeMenuItem==="email"}]),onClick:e[5]||(e[5]=l=>t.activeMenuItem="email")},e[57]||(e[57]=[s("div",{class:"icon"},[s("i",{class:"fas fa-envelope"})],-1),s("div",{class:"label"},"Email",-1)]),2),s("div",{class:r(["menu-item",{active:t.activeMenuItem==="notes"}]),onClick:e[6]||(e[6]=l=>t.activeMenuItem="notes")},e[58]||(e[58]=[s("div",{class:"icon"},[s("i",{class:"fas fa-sticky-note"})],-1),s("div",{class:"label"},"Notes",-1)]),2),s("div",{class:r(["menu-item",{active:t.activeMenuItem==="callLog"}]),onClick:e[7]||(e[7]=l=>t.activeMenuItem="callLog")},e[59]||(e[59]=[s("div",{class:"icon"},[s("i",{class:"fas fa-phone"})],-1),s("div",{class:"label"},"Call Log",-1)]),2),s("div",{class:r(["menu-item",{active:t.activeMenuItem==="webActivity"}]),onClick:e[8]||(e[8]=l=>t.activeMenuItem="webActivity")},e[60]||(e[60]=[s("div",{class:"icon"},[s("i",{class:"fas fa-globe"})],-1),s("div",{class:"label"},"WEB Activity",-1)]),2)]),s("div",F,[e[62]||(e[62]=s("div",{class:"content-header"},[s("h3",null,"Timeline")],-1)),s("div",j,[(d(!0),u(T,null,V(i.activities,(l,A)=>(d(),u("div",{key:A,class:"activity-item"},[e[61]||(e[61]=s("div",{class:"activity-icon"},[s("i",{class:"fas fa-globe"})],-1)),s("div",H,[s("h4",null,p(l.title),1),s("p",null,p(l.description),1),s("div",O,p(l.date)+" "+p(l.time),1)])]))),128))])])])):m("",!0),t.activeTab==="profile"?(d(),u("div",R,[s("div",W,[e[97]||(e[97]=s("h3",null,"Profile",-1)),s("form",{onSubmit:e[45]||(e[45]=C((...l)=>i.updateUser&&i.updateUser(...l),["prevent"]))},[s("div",G,[o(s("input",{type:"hidden","onUpdate:modelValue":e[9]||(e[9]=l=>t.leadId=l)},null,512),[[n,t.leadId]]),s("div",X,[e[63]||(e[63]=s("label",null,"Name:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[10]||(e[10]=l=>t.user.name=l),required:""},null,512),[[n,t.user.name]])]),s("div",Y,[e[64]||(e[64]=s("label",null,"Email:",-1)),o(s("input",{type:"email","onUpdate:modelValue":e[11]||(e[11]=l=>t.user.email=l),required:""},null,512),[[n,t.user.email]])]),s("div",Z,[e[65]||(e[65]=s("label",null,"Phone:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[12]||(e[12]=l=>t.user.phone=l),required:""},null,512),[[n,t.user.phone]])]),s("div",J,[e[66]||(e[66]=s("label",null,"Latest Activity:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[13]||(e[13]=l=>t.user.latestActivity=l)},null,512),[[n,t.user.latestActivity]])]),s("div",K,[e[67]||(e[67]=s("label",null,"Activity At:",-1)),o(s("input",{type:"date","onUpdate:modelValue":e[14]||(e[14]=l=>t.user.activityAt=l)},null,512),[[n,t.user.activityAt]])]),s("div",Q,[e[68]||(e[68]=s("label",null,"Created On:",-1)),o(s("input",{type:"date","onUpdate:modelValue":e[15]||(e[15]=l=>t.user.createdOn=l)},null,512),[[n,t.user.createdOn]])]),s("div",c,[e[69]||(e[69]=s("label",null,"Tags:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[16]||(e[16]=l=>t.user.tags=l)},null,512),[[n,t.user.tags]])]),s("div",h,[e[70]||(e[70]=s("label",null,"Stage:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[17]||(e[17]=l=>t.user.stage=l)},null,512),[[n,t.user.stage]])]),s("div",_,[e[71]||(e[71]=s("label",null,"Latest Source:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[18]||(e[18]=l=>t.user.latestSource=l)},null,512),[[n,t.user.latestSource]])]),s("div",$,[e[72]||(e[72]=s("label",null,"Latest SMS:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[19]||(e[19]=l=>t.user.latestSMS=l)},null,512),[[n,t.user.latestSMS]])]),s("div",ee,[e[73]||(e[73]=s("label",null,"Latest Email:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[20]||(e[20]=l=>t.user.latestEmail=l)},null,512),[[n,t.user.latestEmail]])]),s("div",se,[e[74]||(e[74]=s("label",null,"Next Task:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[21]||(e[21]=l=>t.user.nextTask=l)},null,512),[[n,t.user.nextTask]])]),s("div",te,[e[75]||(e[75]=s("label",null,"Next Appointment:",-1)),o(s("input",{type:"date","onUpdate:modelValue":e[22]||(e[22]=l=>t.user.nextAppointment=l)},null,512),[[n,t.user.nextAppointment]])]),s("div",le,[e[76]||(e[76]=s("label",null,"New Listing Alerts:",-1)),o(s("input",{type:"checkbox","onUpdate:modelValue":e[23]||(e[23]=l=>t.user.newListingAlerts=l)},null,512),[[b,t.user.newListingAlerts]])]),s("div",oe,[e[77]||(e[77]=s("label",null,"Neighbourhood Alerts:",-1)),o(s("input",{type:"checkbox","onUpdate:modelValue":e[24]||(e[24]=l=>t.user.neighbourhoodAlerts=l)},null,512),[[b,t.user.neighbourhoodAlerts]])]),s("div",ne,[e[78]||(e[78]=s("label",null,"Open House Alerts:",-1)),o(s("input",{type:"checkbox","onUpdate:modelValue":e[25]||(e[25]=l=>t.user.openHouseAlerts=l)},null,512),[[b,t.user.openHouseAlerts]])]),s("div",ie,[e[79]||(e[79]=s("label",null,"Price Drop Alerts:",-1)),o(s("input",{type:"checkbox","onUpdate:modelValue":e[26]||(e[26]=l=>t.user.priceDropAlerts=l)},null,512),[[b,t.user.priceDropAlerts]])]),s("div",re,[e[80]||(e[80]=s("label",null,"Active Action Plans:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[27]||(e[27]=l=>t.user.activeActionPlans=l)},null,512),[[n,t.user.activeActionPlans]])]),s("div",ue,[e[81]||(e[81]=s("label",null,"Assigned Market Updates:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[28]||(e[28]=l=>t.user.assignedMarketUpdates=l)},null,512),[[n,t.user.assignedMarketUpdates]])]),s("div",de,[e[82]||(e[82]=s("label",null,"Assigned Newsletters:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[29]||(e[29]=l=>t.user.assignedNewsletters=l)},null,512),[[n,t.user.assignedNewsletters]])]),s("div",ae,[e[83]||(e[83]=s("label",null,"Notes:",-1)),o(s("textarea",{"onUpdate:modelValue":e[30]||(e[30]=l=>t.user.notes=l)},null,512),[[n,t.user.notes]])]),s("div",pe,[e[84]||(e[84]=s("label",null,"Date of Birth:",-1)),o(s("input",{type:"date","onUpdate:modelValue":e[31]||(e[31]=l=>t.user.dob=l)},null,512),[[n,t.user.dob]])]),s("div",me,[e[85]||(e[85]=s("label",null,"Background:",-1)),o(s("textarea",{"onUpdate:modelValue":e[32]||(e[32]=l=>t.user.background=l)},null,512),[[n,t.user.background]])]),s("div",ve,[e[86]||(e[86]=s("label",null,"City:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[33]||(e[33]=l=>t.user.city=l)},null,512),[[n,t.user.city]])]),s("div",be,[e[87]||(e[87]=s("label",null,"Facebook:",-1)),o(s("input",{type:"url","onUpdate:modelValue":e[34]||(e[34]=l=>t.user.facebook=l)},null,512),[[n,t.user.facebook]])]),s("div",ge,[e[88]||(e[88]=s("label",null,"Instagram:",-1)),o(s("input",{type:"url","onUpdate:modelValue":e[35]||(e[35]=l=>t.user.instagram=l)},null,512),[[n,t.user.instagram]])]),s("div",fe,[e[89]||(e[89]=s("label",null,"LinkedIn:",-1)),o(s("input",{type:"url","onUpdate:modelValue":e[36]||(e[36]=l=>t.user.linkedin=l)},null,512),[[n,t.user.linkedin]])]),s("div",ye,[e[90]||(e[90]=s("label",null,"WhatsApp:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[37]||(e[37]=l=>t.user.whatsapp=l)},null,512),[[n,t.user.whatsapp]])]),s("div",ke,[e[91]||(e[91]=s("label",null,"Twitter:",-1)),o(s("input",{type:"url","onUpdate:modelValue":e[38]||(e[38]=l=>t.user.twitter=l)},null,512),[[n,t.user.twitter]])]),s("div",Ae,[e[92]||(e[92]=s("label",null,"House Number:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[39]||(e[39]=l=>t.user.house_number=l),required:""},null,512),[[n,t.user.house_number]])]),s("div",Ue,[e[93]||(e[93]=s("label",null,"Street:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[40]||(e[40]=l=>t.user.street=l),required:""},null,512),[[n,t.user.street]])]),s("div",Te,[e[94]||(e[94]=s("label",null,"City:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[41]||(e[41]=l=>t.user.city=l),required:""},null,512),[[n,t.user.city]])]),s("div",Ve,[e[95]||(e[95]=s("label",null,"Province:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[42]||(e[42]=l=>t.user.province=l),required:""},null,512),[[n,t.user.province]])]),s("div",Ce,[e[96]||(e[96]=s("label",null,"Zip Code:",-1)),o(s("input",{type:"text","onUpdate:modelValue":e[43]||(e[43]=l=>t.user.zip_code=l),required:""},null,512),[[n,t.user.zip_code]])])]),s("button",{type:"submit",class:"btn-submit",onClick:e[44]||(e[44]=(...l)=>v.updateLead&&v.updateLead(...l))},"Update")],32)])])):m("",!0),t.activeTab==="analytics"?(d(),u("div",Ie,[s("div",Le,[s("ul",null,[s("li",{class:r({active:i.selectedTab==="for-sale"}),onClick:e[46]||(e[46]=l=>i.selectedTab="for-sale")},"For Sale",2),s("li",{class:r({active:i.selectedTab==="sold"}),onClick:e[47]||(e[47]=l=>i.selectedTab="sold")},"Sold",2),s("li",{class:r({active:i.selectedTab==="rent"}),onClick:e[48]||(e[48]=l=>i.selectedTab="rent")},"Rent",2),s("li",{class:r({active:i.selectedTab==="leased"}),onClick:e[49]||(e[49]=l=>i.selectedTab="leased")},"Leased",2),s("li",{class:r({active:i.selectedTab==="pre-construction"}),onClick:e[50]||(e[50]=l=>i.selectedTab="pre-construction")},"Pre-Construction",2)])]),s("div",we,[s("div",Me,[i.selectedTab==="for-sale"?(d(),u("div",Se,e[98]||(e[98]=[s("h4",null,"For Sale Analytics Content",-1)]))):m("",!0),i.selectedTab==="sold"?(d(),u("div",xe,e[99]||(e[99]=[s("h4",null,"Sold Analytics Content",-1)]))):m("",!0),i.selectedTab==="rent"?(d(),u("div",Ne,e[100]||(e[100]=[s("h4",null,"Rent Analytics Content",-1)]))):m("",!0),i.selectedTab==="leased"?(d(),u("div",Pe,e[101]||(e[101]=[s("h4",null,"Leased Analytics Content",-1)]))):m("",!0),i.selectedTab==="pre-construction"?(d(),u("div",Ee,e[102]||(e[102]=[s("h4",null,"Pre-Construction Analytics Content",-1)]))):m("",!0)])])])):m("",!0),e[103]||(e[103]=k('<div class="footer" data-v-9a97e57d><div class="copyright" data-v-9a97e57d> Copyright © 2025 <a href="#" data-v-9a97e57d>Agentroof</a>. All rights reserved. </div></div><div class="fab" data-v-9a97e57d><button class="fab-btn" data-v-9a97e57d><i class="fas fa-plus" data-v-9a97e57d></i></button></div>',2))])}const Be=U(I,[["render",ze],["__scopeId","data-v-9a97e57d"]]);export{Be as default};
