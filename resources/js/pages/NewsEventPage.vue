<script setup>
import {computed,onMounted,ref,watch} from 'vue';
import {useRoute} from 'vue-router';
import SiteHeader from '../components/SiteHeader.vue';
import SiteFooter from '../components/SiteFooter.vue';
import {getCollegeContent,getEvent,getEvents} from '../services/api';

const route=useRoute(),site=ref({}),event=ref(null),events=ref([]),loading=ref(true),listLoading=ref(false),notFound=ref(false),search=ref('');
let searchTimer;
const otherEvents=computed(()=>events.value.filter(item=>item.slug!==route.params.slug));
const hasSearch=computed(()=>search.value.trim().length>0);
const formattedDate=(date)=>{if(!date)return'Latest update';const parsed=new Date(String(date).length===10?`${date}T12:00:00`:date);return Number.isNaN(parsed.getTime())?'Latest update':new Intl.DateTimeFormat('en-US',{year:'numeric',month:'long',day:'numeric'}).format(parsed)};

async function loadArticle(){
  loading.value=true;
  notFound.value=false;
  event.value=null;
  try{
    event.value=(await getEvent(route.params.slug)).data.data;
    document.title=event.value.title;
  }catch(error){
    notFound.value=error.response?.status===404;
  }finally{
    loading.value=false;
  }
}

async function loadEvents(){
  listLoading.value=true;
  try{
    events.value=(await getEvents({search:search.value.trim()})).data.data;
  }catch{
    events.value=[];
  }finally{
    listLoading.value=false;
  }
}

watch(()=>route.params.slug,async()=>{await loadArticle();await loadEvents()});
watch(search,()=>{window.clearTimeout(searchTimer);searchTimer=window.setTimeout(loadEvents,300)});
onMounted(async()=>{getCollegeContent().then(response=>{site.value=response.data.data.site}).catch(()=>{});await Promise.all([loadArticle(),loadEvents()])});
</script>

<template><SiteHeader :site="site" active="news & events"/><main class="event-page"><div v-if="loading" class="event-state"><div class="spinner-border text-primary"></div><p>Loading article...</p></div><section v-else-if="notFound||!event" class="event-state"><i class="bi bi-calendar-x"></i><h1>News or event not found</h1><p>This article may no longer be published.</p><router-link to="/news-events" class="btn btn-primary">Return to News & Events</router-link></section><template v-else><section class="event-hero"><div class="container"><router-link to="/news-events" class="event-back"><i class="bi bi-arrow-left"></i> All News & Events</router-link><span>NEWS & EVENTS</span><h1>{{event.title}}</h1><div class="event-meta"><i class="bi bi-calendar3"></i> {{formattedDate(event.event_date)}} <b>-</b> {{site.college_short_name||site.college_name||'College'}}</div></div></section><article class="event-article event-detail-layout"><div class="container"><img class="event-cover" :src="event.image_url||'/images/nursing-hero.png'" :alt="event.title"><div class="event-detail-grid"><div class="event-content"><p class="event-lead">{{event.excerpt}}</p><div class="event-body">{{event.body||event.excerpt}}</div><div class="event-share"><span>Have questions about this update?</span><router-link to="/contact" class="btn btn-primary">Talk to Admissions</router-link></div></div><aside class="related-news"><div class="related-news-header"><h2>More News</h2><router-link to="/news-events">View all</router-link></div><label class="news-search related-search"><i class="bi bi-search"></i><input v-model="search" type="search" class="form-control" placeholder="Search news..." aria-label="Search other news"></label><div v-if="listLoading" class="related-state"><div class="spinner-border spinner-border-sm text-primary"></div><span>Searching...</span></div><div v-else-if="!otherEvents.length" class="related-state"><i class="bi bi-search"></i><span>{{hasSearch?'No matching news found.':'No other news available.'}}</span></div><div v-else class="related-list"><router-link v-for="item in otherEvents" :key="item.id" class="related-item" :to="`/news-events/${item.slug}`"><img :src="item.image_url||'/images/nursing-hero.png'" :alt="item.title"><span>{{formattedDate(item.event_date)}}</span><b>{{item.title}}</b></router-link></div></aside></div></div></article></template></main><SiteFooter :site="site"/></template>
