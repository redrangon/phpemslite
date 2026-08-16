<template>
	<div class="courseTree">
		<template v-if="treeData.childNodes && treeData.childNodes.length">
			<TreeNode :hideProgress="hideProgress" v-for="node in treeData.childNodes" :expandOnClickNode="expandOnClickNode" :key="getNodeKey(node)" :node="node" @node-select="handleNodeSelect" />
		</template>
		<div class="courseTree__empty" v-else>
			<span class="courseTree__empty-text">暂无数据</span>
		</div>
	</div>
</template>

<script>
	import TreeNode from "./TreeNode.vue";
	export default {
		components: {
			TreeNode,
		},
		provide() {
			return {
				tree: this.treeInstance,
			};
		},
		props: {
			data: {
				// parent passes an array (Object.values(...)) so accept Array.
				// also accept Object in case an object map is passed.
				type: [Array, Object],
				required: true,
			},
			expandOnClickNode: {
				type: Boolean,
				default: true,
			},
            hideProgress: {
                type: Boolean,
                default: false,
            },
		},
		data() {
			return {
				selectedNode: null,
				treeData: {
					childNodes: [],
					level: 0,
				},
				treeInstance: {
					indent: 16,
					nodeKey: "id",
					props: {},
					store: {
						currentNode: null,
						setCurrentNode: (node) => {
							this.treeInstance.store.currentNode = node;
						},
					},
					expandOnClickNode: true,
					emit: (event, ...args) => {
						//console.log("Tree event:", event, args);
						this.$emit(event, args[0], args[1]);
					},
				},
			};
		},
		// Rebuild the internal tree whenever `data` changes (supports async updates)
		watch: {
			data: {
				immediate: true,
				handler(newVal) {
					const source = Array.isArray(newVal) ? newVal : Object.values(newVal ?? {});
					this.treeData.childNodes = this.createTreeNodes(source || []);
				},
			},
		},
		methods: {
			createTreeNodes(data, parent = null, level = 0) {
				return data.map((item) => {
					const node = {
						id: item.courseid,
						courseid: item.courseid,
						coursetitle: item.coursetitle,
						logstatus:item.logstatus??0,
						logprogress:item.logprogress??0,
						iscurrent:item.iscurrent??false,
						coursemoduleid:item.coursemoduleid,
						csid:item.coursecsid,
						level: level + 1,
						visible: true,
						isCurrent: item.isCurrent,
						disabled: !!item.disabled,
						isLeaf: !item.children || item.children.length === 0,
						expanded: item.expanded,
						childNodes: [],
						data: item,
						loading: false,
						expand: () => {},
						collapse: () => {},
					};

					if (item.children && item.children.length > 0) {
						node.childNodes = this.createTreeNodes(
							item.children,
							node,
							level + 1
						);
					}
					return node;
				});
			},
			getNodeKey(node) {
				return node.id;
			},
			handleNodeSelect(node) {
				if (this.selectedNode) {
					this.selectedNode.isCurrent = false;
				}
				node.isCurrent = true;
				this.selectedNode = node;
				this.$emit("current-change", node.data, node);
			},
		},
	};
</script>

<style scoped>
	.courseTree {
		width: 100%;
		height: 100%;
	}

	.courseTree__empty {
		display: flex;
		justify-content: center;
		align-items: center;
		color: #909399;
		font-size: 28rpx;
	}
</style>