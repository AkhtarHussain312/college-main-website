<script setup>defineProps({eyebrow:String,title:String,text:String,light:Boolean});</script>
<template><div class="section-heading" :class="{light}"><span v-if="eyebrow" class="eyebrow">{{eyebrow}}</span><h2>{{title}}</h2><p v-if="text">{{text}}</p></div></template>
