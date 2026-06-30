<template>
	<div class="courseTree">
		<template v-if="data.length">
			<TreeNode v-for="node in treeData.childNodes" :expandOnClickNode="expandOnClickNode" :key="getNodeKey(node)" :node="node" @node-select="handleNodeSelect" />
		</template>
		<div class="courseTree__empty" v-else>
			<span class="courseTree__empty-text">暂无数据</span>
		</div>
	</div>
</template>

<script>
import TreeNode from "@/components/master/TreeNode.vue";

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
				type: Object,
				required: true,
			},
			expandOnClickNode: {
				type: Boolean,
				default: true,
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
						this.$emit(event, args[0], args[1]);
					},
				},
			};
		},
		created() {
			this.treeData.childNodes = this.createTreeNodes(this.data);
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
						coursemodule:item.coursemodule,
						csid:item.coursecsid,
						level: level + 1,
						visible: true,
						isCurrent: false,
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