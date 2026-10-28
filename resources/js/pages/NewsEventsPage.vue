<script setup>
import {computed,onMounted,ref,watch} from 'vue';
import SiteHeader from '../components/SiteHeader.vue';
import SiteFooter from '../components/SiteFooter.vue';
import {getCollegeContent,getEvents} from '../services/api';

const site=ref({}),events=ref([]),loading=ref(true),search=ref(''),error=ref('');
let searchTimer;
const hasSearch=computed(()=>search.value.trim().length>0);
const formattedDate=(date)=>{if(!date)return'Latest update';const parsed=new Date(String(date).length===10?`${date}T12:00:00`:date);return Number.isNaN(parsed.getTime())?'Latest update':new Intl.DateTimeFormat('en-US',{year:'numeric',month:'long',day:'numeric'}).format(parsed)};
async function loadEvents(){loading.value=true;error.value='';try{events.value=(await getEvents({search:search.value.trim()})).data.data}catch{error.value='News could not be loaded. Please try again.'}finally{loading.value=false}}
watch(search,()=>{window.clearTimeout(searchTimer);searchTimer=window.setTimeout(loadEvents,300)});
onMounted(async()=>{getCollegeContent().then(response=>{site.value=response.data.data.site}).catch(()=>{});document.title='News & Events';await loadEvents()});
</script>

<template><SiteHeader :site="site" active="news & events"/><main class="news-index"><section class="section section-compact pt-4 pt-md-5"><div class="container"><nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb custom-breadcrumb"><li class="breadcrumb-item"><router-link to="/"><i class="bi bi-house-door me-1"></i>Home</router-link></li><li class="breadcrumb-item active" aria-current="page">News & Events</li></ol></nav><div class="news-toolbar mt-0 mb-4"><div><span class="eyebrow">CAMPUS BULLETINS</span><h1 class="page-title mb-1">Latest and Previous News</h1><p class="text-muted mb-0">{{loading?'Loading updates...':`${events.length} ${events.length===1?'bulletin found':'bulletins found'}`}}</p></div><label class="news-search"><i class="bi bi-search"></i><input v-model="search" type="search" class="form-control" placeholder="Search news..." aria-label="Search news and events"></label></div><div v-if="error" class="alert alert-danger">{{error}}</div><div v-if="loading" class="news-state"><div class="spinner-border text-primary"></div><p>Loading news...</p></div><div v-else-if="!events.length" class="news-state"><i class="bi bi-search"></i><h2>No news found</h2><p>{{hasSearch?'Try another search term.':'No published news is available yet.'}}</p></div><div v-else class="row g-4"><div class="col-md-6 col-xl-4" v-for="event in events" :key="event.id"><router-link class="news-card-link" :to="`/news-events/${event.slug}`" :aria-label="`Read ${event.title}`"><article class="news-card news-list-card"><img :src="event.image_url||'/images/nursing-hero.png'" :alt="event.title"><div class="p-4"><small>{{formattedDate(event.event_date)}}</small><h3>{{event.title}}</h3><p>{{event.excerpt}}</p><span class="news-read-more">Read Detail <i class="bi bi-arrow-right"></i></span></div></article></router-link></div></div></div></section></main><SiteFooter :site="site"/></template>
