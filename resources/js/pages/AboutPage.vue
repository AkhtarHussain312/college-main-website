<script setup>
import {computed,onMounted,ref} from 'vue';
import SiteHeader from '../components/SiteHeader.vue';
import SiteFooter from '../components/SiteFooter.vue';
import SectionTitle from '../components/SectionTitle.vue';
import {getCollegeContent} from '../services/api';

const data=ref({site:{},programs:[],faculty:[],milestones:[],about_sections:[]}),loading=ref(true);
const value=(key,fallback)=>data.value.site?.[key]||fallback;
const fallbackSections=computed(()=>[
  {
    id:'story',
    title:value('about_title','Preparing Compassionate Healthcare Professionals'),
    body:value('about_text','We are committed to building confident, skilled, and ethical healthcare professionals through quality education, supervised practice, and a supportive campus environment.'),
    image_url:value('about_image','/images/nursing-hero.png'),
    image_position:'left'
  }
]);
const aboutSections=computed(()=>data.value.about_sections?.length?data.value.about_sections:fallbackSections.value);

onMounted(async()=>{
  try{
    data.value=(await getCollegeContent()).data.data;
    document.title=`About | ${value('college_name','College of Nursing')}`;
  }finally{
    loading.value=false;
  }
});
</script>

<template><SiteHeader :site="data.site" active="about us"/><main><section class="about-hero"><div class="container"><span class="eyebrow text-info">ABOUT OUR COLLEGE</span><h1>{{value('college_name','Dir College Of Nursing and Allied Health Sciences')}}</h1><p>{{value('about_text','Our college combines academic excellence, practical clinical training, modern facilities, and compassionate mentorship to prepare students for meaningful healthcare careers.')}}</p></div></section><section class="section about-dynamic"><div class="container"><div v-if="loading" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading about sections</span></div></div><div v-else class="about-section-stack"><article v-for="(section,index) in aboutSections" :key="section.id||section.title" class="about-story-block" :class="{'is-reversed':section.image_position==='right'}"><div class="about-story-media"><img class="about-page-image" :src="section.image_url||value('about_image','/images/nursing-hero.png')" :alt="section.title"></div><div class="about-story-copy"><span class="eyebrow">{{index===0?'OUR STORY':'ABOUT US'}}</span><h2>{{section.title}}</h2><p>{{section.body}}</p><router-link v-if="index===0" to="/apply" class="btn btn-primary">Apply Now <i class="bi bi-arrow-right"></i></router-link></div></article></div></div></section><section class="section section-soft"><div class="container"><SectionTitle title="What Makes Us Different" text="A focused learning environment for students who want strong academics and real professional preparation."/><div class="row g-4"><div class="col-md-6 col-lg-3" v-for="item in [['bi-hospital','Clinical Practice','Students learn through practical training and supervised healthcare exposure.'],['bi-mortarboard','Academic Excellence','Structured teaching helps students build strong professional foundations.'],['bi-people','Supportive Faculty','Experienced instructors guide students with care and discipline.'],['bi-graph-up-arrow','Career Direction','Programs are aligned with healthcare career growth and future study.']]" :key="item[1]"><article class="about-value-card"><i :class="['bi',item[0]]"></i><h3>{{item[1]}}</h3><p>{{item[2]}}</p></article></div></div></div></section><section class="about-cta"><div class="container text-center"><span class="eyebrow text-info">VISIT OUR CAMPUS</span><h2>See the environment where your healthcare journey can begin.</h2><router-link to="/contact" class="btn btn-primary btn-lg">Contact Admissions</router-link></div></section></main><SiteFooter :site="data.site"/></template>
