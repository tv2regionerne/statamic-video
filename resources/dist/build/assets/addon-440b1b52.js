function p(e,t,s,a,r,d,u,c){var i=typeof e=="function"?e.options:e;t&&(i.render=t,i.staticRenderFns=s,i._compiled=!0),a&&(i.functional=!0),d&&(i._scopeId="data-v-"+d);var o;if(u?(o=function(n){n=n||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,!n&&typeof __VUE_SSR_CONTEXT__<"u"&&(n=__VUE_SSR_CONTEXT__),r&&r.call(this,n),n&&n._registeredComponents&&n._registeredComponents.add(u)},i._ssrRegister=o):r&&(o=c?function(){r.call(this,(i.functional?this.parent:this).$root.$options.shadowRoot)}:r),o)if(i.functional){i._injectStyles=o;var f=i.render;i.render=function(_,h){return o.call(h),f(_,h)}}else{var l=i.beforeCreate;i.beforeCreate=l?[].concat(l,o):[o]}return{exports:e,options:i}}const v={mixins:[Fieldtype],inject:["storeName"],data(){return{loading:!0,pos:0,selected:0,chapters:this.value.chapters??[]}},computed:{sourceUrl(){return this.store.values[this.config.source]},store(){return this.$store.state.publish[this.storeName]},duration(){return this.$refs.video.duration},time(){return Math.round(this.duration*this.pos)}},methods:{loadedVideo(){this.loading=!1},seekVideo(e){this.$refs.video.pause(),this.$refs.video.currentTime=this.duration*e},updatePos(e){this.pos=e,this.seekVideo(this.pos),this.updateChapter(this.selected,{start:this.time})},updateTime(e){this.updatePos(e/this.duration)},addChapter(){this.chapters=[...this.chapters,{start:this.time,title:null}],this.selectChapter(this.chapters.length-1)},selectChapter(e){this.selected=e,this.updateTime(this.chapters[e].start)},updateChapter(e,t){this.chapters=[...this.chapters.slice(0,e),{...this.chapters[e],...t},...this.chapters.slice(e+1)]},updateValue(){this.update({chapters:this.chapters})}},watch:{chapters(){this.updateValue()}}};var m=function(){var t=this,s=t._self._c;return s("div",[s("video",{ref:"video",staticClass:"w-full rounded mb-2",attrs:{src:t.sourceUrl,controls:""},on:{loadeddata:t.loadedVideo}}),t.loading?t._e():s("div",{staticClass:"video-Text-controls flex"},[s("input",{staticClass:"w-full",attrs:{type:"range",min:"0",max:"1",step:"any"},domProps:{value:t.pos},on:{input:function(a){return t.updatePos(a.target.value)}}}),s("button",{staticClass:"btn-xs btn ml-2",on:{click:t.addChapter}},[t._v(" Add ")])]),t.loading?t._e():s("div",{staticClass:"video-Text-chapters"},t._l(t.chapters,function(a,r){return s("div",{staticClass:"flex mt-2 cursor-pointer rounded flex items-center p-1",class:{"bg-gray-400":r!==t.selected,"bg-blue text-white":r===t.selected},on:{click:function(d){return t.selectChapter(r)}}},[s("div",{staticClass:"w-32 px-2",attrs:{type:"text",isReadOnly:""}},[t._v(" "+t._s(a.start)+" ")]),s("input",{staticClass:"grow px-2 py-1 rounded bg-gray-100 text-gray-800",attrs:{type:"text",placeholder:"Title"},domProps:{value:a.title},on:{input:function(d){return t.updateChapter(r,{title:d.target.value})}}})])}),0)])},g=[],C=p(v,m,g,!1,null,null,null,null);const $=C.exports;const y={mixins:[Fieldtype],inject:["storeName"],data(){return{loading:!0,positions:{}}},computed:{sourceUrl(){return this.config.source_url||this.store.values[this.config.source]},store(){return this.$store.state.publish[this.storeName]},duration(){return this.$refs.video.duration},isSingle(){return this.config.mode==="single"},isRange(){return this.config.mode==="range"},times(){return{start:Math.round(this.duration*this.positions.start),end:Math.round(this.duration*this.positions.end)}}},methods:{loadedVideo(){this.loading=!1,this.positions=this.value!==null?{start:this.value.start/this.duration,end:this.value.end/this.duration}:{start:0,end:1}},seekVideo(e){this.$refs.video.pause(),this.$refs.video.currentTime=this.duration*e},updatePosition(e,t){this.positions[e]=t,e==="start"&&(this.positions.end=Math.max(this.positions.end,t)),e==="end"&&(this.positions.start=Math.min(this.positions.start,t)),this.seekVideo(t),this.updateValue()},updateValue(){this.updateDebounced(this.times)}}};var x=function(){var t=this,s=t._self._c;return s("div",{},[s("video",{ref:"video",staticClass:"w-full rounded-t",attrs:{src:t.sourceUrl,controls:""},on:{loadeddata:t.loadedVideo}}),t.loading?t._e():s("div",{staticClass:"flex bg-gray-900 p-2 gap-2 rounded-b -mt-px text-white items-center"},[s("span",{staticClass:"w-10 shrink-0 text-center text-xs"},[t._v(t._s(t.times.start)+"s")]),s("input",{staticClass:"w-full",attrs:{type:"range",min:"0",max:"1",step:"any"},domProps:{value:t.positions.start},on:{input:function(a){return t.updatePosition("start",a.target.value)}}}),t.isRange?s("input",{staticClass:"w-full",attrs:{type:"range",min:"0",max:"1",step:"any"},domProps:{value:t.positions.end},on:{input:function(a){return t.updatePosition("end",a.target.value)}}}):t._e(),t.isRange?s("span",{staticClass:"w-10 shrink-0 text-center text-xs"},[t._v(t._s(t.times.end)+"s")]):s("span")])])},V=[],b=p(y,x,V,!1,null,null,null,null);const T=b.exports;Statamic.booting(()=>{Statamic.component("video_text-fieldtype",$),Statamic.component("video_time-fieldtype",T)});
