<script setup lang="ts">
</script>
<template>
	<div class="page">
		<div class="page-container">
			<div class="bg-decoration">
				<div class="bg-circle bg-circle-1"></div>
				<div class="bg-circle bg-circle-2"></div>
				<div class="bg-pattern bg-pattern-1">
					<van-icon name="books" />
				</div>
				<div class="bg-pattern bg-pattern-2">
					<van-icon name="graduation-cap" />
				</div>
				<div class="bg-pattern bg-pattern-3">
					<van-icon name="star" />
				</div>
			</div>
			<router-view />
		</div>
	</div>
</template>
<style src="@/assets/css/mobile/auth.css"></style>