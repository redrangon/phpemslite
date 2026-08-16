<script setup>
</script>
<template>
	<router-view v-slot="{ Component, route }">
        <component :is="Component"/>
	</router-view>
</template>
<style>
@import "@/assets/css/desktop/master.css";
</style>
