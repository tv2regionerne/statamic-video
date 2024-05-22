function m(e,t,s,n,i,r,u,h){var a=typeof e=="function"?e.options:e;t&&(a.render=t,a.staticRenderFns=s,a._compiled=!0),n&&(a.functional=!0),r&&(a._scopeId="data-v-"+r);var d;if(u?(d=function(o){o=o||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,!o&&typeof __VUE_SSR_CONTEXT__<"u"&&(o=__VUE_SSR_CONTEXT__),i&&i.call(this,o),o&&o._registeredComponents&&o._registeredComponents.add(u)},a._ssrRegister=d):i&&(d=h?function(){i.call(this,(a.functional?this.parent:this).$root.$options.shadowRoot)}:i),d)if(a.functional){a._injectStyles=d;var p=a.render;a.render=function(f,c){return d.call(c),p(f,c)}}else{var l=a.beforeCreate;a.beforeCreate=l?[].concat(l,d):[d]}return{exports:e,options:a}}const v={mixins:[Fieldtype],inject:["storeName"],data(){return{loading:!0,selected:0,items:{}}},computed:{sourceUrl(){return this.store.values[this.config.source]},store(){return this.$store.state.publish[this.storeName]},duration(){return Math.round(this.$refs.video.duration)},positions(){return this.items.map(e=>e.start/this.duration)}},methods:{loadedVideo(){this.loading=!1,this.items=this.value},addItem(){this.items=[...this.items,{start:Math.min(this.items[this.items.length-1].start+1,this.duration),text:null}],this.updateValue(),this.selectItem(this.items.length-1)},seekVideo(e){this.$refs.video.pause(),this.$refs.video.currentTime=e},updateItemStart(e,t){var r,u;const s=e!==0&&((r=this.items[e-1])==null?void 0:r.start)||0,n=e!==0?((u=this.items[e+1])==null?void 0:u.start)||this.duration:0,i=Math.min(n,Math.max(s,parseInt(t)));this.updateItem(e,{start:i}),this.seekVideo(i)},updateItem(e,t){this.items=[...this.items.slice(0,e),{...this.items[e],...t},...this.items.slice(e+1)],this.updateValue()},updateValue(){this.update(this.items)},deleteItem(e){this.items=[...this.items.slice(0,e),...this.items.slice(e+1)],this.updateValue(),this.selectItem(0)},selectItem(e){this.selected=e},timecode(e){const t=Math.floor(e/60),s=e%60;return`${t.toString().padStart(2,"0")}:${s.toString().padStart(2,"0")}`}},watch:{selected(){const e=this.positions[this.selected];this.seekVideo(e)}}};var _=function(){var t=this,s=t._self._c;return s("div",[s("video",{ref:"video",staticClass:"w-full rounded-t",attrs:{src:t.sourceUrl,controls:""},on:{loadeddata:t.loadedVideo}}),t.loading?t._e():s("div",{staticClass:"flex bg-gray-900 p-2 gap-2 rounded-b -mt-px text-white items-center"},[s("div",{staticClass:"video_addon-track video_text-track"},[s("input",{staticClass:"w-full",attrs:{type:"range",min:0,max:t.duration},domProps:{value:t.items[t.selected].start},on:{input:function(n){return t.updateItemStart(t.selected,n.target.value)}}}),t._l(t.items,function(n,i){var r;return s("div",{directives:[{name:"tooltip",rawName:"v-tooltip",value:n.text||"Unnamed",expression:"item.text || 'Unnamed'"}],style:{"--start":n.start/t.duration,"--end":(((r=t.items[i+1])==null?void 0:r.start)||t.duration)/t.duration},on:{click:function(u){return t.selectItem(i)}}})})],2)]),t.loading?t._e():s("div",[t._l(t.items,function(n,i){return s("div",{staticClass:"mt-2 cursor-pointer relative video_text-item",class:{"video_text-selected":i===t.selected},on:{click:function(r){return t.selectItem(i)}}},[s("text-input",{staticClass:"w-full",attrs:{prepend:t.timecode(n.start),placeholder:"Text",value:n.text},on:{focus:function(r){return t.selectItem(i)},input:function(r){return t.updateItem(i,{text:r})}}}),i!==0?s("button",{staticClass:"text-gray-600 cursor-pointer px-2 hover:text-blue-500",attrs:{type:"button"},on:{click:function(r){return t.deleteItem(i)}}},[s("span",[t._v("×")])]):t._e()],1)}),s("button",{staticClass:"btn mt-2",on:{click:t.addItem}},[t._v("Add Chapter")])],2)])},g=[],$=m(v,_,g,!1,null,null,null,null);const C=$.exports,I={mixins:[Fieldtype],inject:["storeName"],data(){return{loading:!0,item:null}},computed:{sourceUrl(){return this.config.source_url||this.store.values[this.config.source]},store(){return this.$store.state.publish[this.storeName]},duration(){return Math.round(this.$refs.video.duration)},isSingle(){return this.config.mode==="single"},isRange(){return this.config.mode==="range"}},methods:{loadedVideo(){this.loading=!1,this.item=this.value.start!==null||this.value.end!==null?this.value:{start:0,end:this.duration}},seekVideo(e){this.$refs.video.pause(),this.$refs.video.currentTime=e},updateTime(e,t){let s;if(this.isRange){const n=e==="start"?0:this.item.start,i=e==="start"?this.item.end:this.duration;s=Math.min(i,Math.max(n,parseInt(t)))}else s=parseInt(t);this.updateItem({[e]:s}),this.seekVideo(s)},updateItem(e){this.item={...this.item,...e},this.updateValue()},updateValue(){this.updateDebounced(this.config.null_limits?{start:this.item.start>0?this.item.start:null,end:this.item.end<this.duration?this.item.end:null}:this.item)},timecode(e){const t=Math.floor(e/60),s=e%60;return`${t.toString().padStart(2,"0")}:${s.toString().padStart(2,"0")}`}}};var V=function(){var t=this,s=t._self._c;return s("div",{},[s("video",{ref:"video",staticClass:"w-full rounded-t",attrs:{src:t.sourceUrl,controls:""},on:{loadeddata:t.loadedVideo}}),t.loading?t._e():s("div",{staticClass:"flex bg-gray-900 p-2 gap-2 rounded-b -mt-px text-white items-center"},[s("div",{staticClass:"w-12 shrink-0 text-center text-xs"},[t._v(" "+t._s(t.timecode(t.item.start))+" ")]),s("div",{staticClass:"video_addon-track video_time-track"},[s("input",{staticClass:"w-full",attrs:{type:"range",min:0,max:t.duration},domProps:{value:t.item.start},on:{input:function(n){return t.updateTime("start",n.target.value)}}}),t.isRange?s("div",{style:{"--start":t.item.start/t.duration,"--end":t.item.end/t.duration}}):t._e(),t.isRange?s("input",{staticClass:"w-full",attrs:{type:"range",min:0,max:t.duration},domProps:{value:t.item.end},on:{input:function(n){return t.updateTime("end",n.target.value)}}}):t._e()]),t.isRange?s("div",{staticClass:"w-12 shrink-0 text-center text-xs"},[t._v(" "+t._s(t.timecode(t.item.end))+" ")]):t._e()])])},x=[],S=m(I,V,x,!1,null,null,null,null);const b=S.exports;Statamic.booting(()=>{Statamic.component("video_text-fieldtype",C),Statamic.component("video_time-fieldtype",b)});