// frontend/env.d.ts （路径要被 tsconfig.include 覆盖）
declare module '*.vue' {
	import type { DefineComponent } from 'vue';
	const component: DefineComponent<{}, {}, any>;
	export default component;
}