<template>
	<div class="courseTree-node" @click.stop v-show="node.visible" :class="{'is-expanded': expanded,'is-current': node.isCurrent,'is-hidden': !node.visible,'is-disabled': node.disabled,}" role="treeitem">
		<div class="courseTree-node__content" :style="{ 'padding-left': (node.level - 1) * tree.indent + 'px' }" @click.stop="handleContentClick">
			<div @click.stop="handleExpandIconClick" :class="[ { 'is-leaf': node.isLeaf, expanded: !node.isLeaf && expanded }, 'courseTree-node__expand-icon', ]">
				<lay-icon type="layui-icon-right" v-if="!node.isLeaf"></lay-icon>
			</div>
			<div class="courseTree-node__label" v-if="node.coursemodule === 'dir'">
				{{node.coursetitle}}
			</div>
			<div class="courseTree-node__label" v-else>
				<a href="javascript:;" hover-class="none">
					<div class="intro paddinglr">
						<lay-icon type="layui-icon-list" v-if="node.coursemodule === 'html'" style="margin-right: 8px;"></lay-icon>
						<lay-icon type="layui-icon-play" v-if="node.coursemodule === 'video'" style="margin-right: 8px;"></lay-icon>
						<lay-icon type="layui-icon-file-b" v-if="node.coursemodule === 'pdf'" style="margin-right: 8px;"></lay-icon>
						<lay-icon type="layui-icon-file-b" v-if="node.coursemodule === 'md'" style="margin-right: 8px;"></lay-icon>
						{{node.coursetitle}}
					</div>
				</a>
			</div>
		</div>
		<div class="courseTree-node__children" v-if="!renderAfterExpand || childNodeRendered" v-show="expanded" role="group">
			<TreeNode v-for="child in node.childNodes" :key="getNodeKey(child)" :node="child" :render-after-expand="renderAfterExpand" :expand-on-click-node="expandOnClickNode" @node-expand="handleChildNodeExpand" @node-select="handleChildNodeSelect" />
		</div>
	</div>
</template>

<script>
export default {
	name: "TreeNode",
	props: {
		node: {
			type: Object,
			required: true,
		},
		renderAfterExpand: {
			type: Boolean,
			default: true,
		},
		expandOnClickNode: {
			type: Boolean,
			default: true,
		},
	},
	inject: ["tree"],
	data() {
		return {
			expanded: this.node.expanded || false,
			childNodeRendered: false
		};
	},
	created() {
		this.node.expand = this.expand;
		this.node.collapse = this.collapse;

		if (this.node.expanded) {
			this.childNodeRendered = true;
		}
	},
	watch: {
		"node.expanded"(val) {
			this.expanded = val;
				if (val && !this.childNodeRendered) {
				this.childNodeRendered = true;
			}
		},
	},
	methods: {
		getNodeKey(node) {
			return node.data[this.tree.nodeKey] || node.id;
		},
		handleContentClick() {
			if (this.expandOnClickNode) {
				if (this.node.isLeaf) {
					// 叶子节点 => 选中
					this.selectNode();
				} else {
					// 父节点 => 展开/折叠，不选中
					this.handleExpandIconClick();
				}
			} else {
				// 无论是否为叶子节点 => 选中，不展开
				this.selectNode();
			}
			this.tree.emit("node-click", this.node.data, this.node);
		},
		handleExpandIconClick() {
			if (this.node.isLeaf) return;
			this.expanded ? this.collapse() : this.expand();
		},
		expand() {
			this.expanded = true;
			this.node.expanded = true;
			if (!this.childNodeRendered) {
				this.childNodeRendered = true;
			}
			this.$emit("node-expand", this.node.data, this.node);
			this.tree.emit("node-expand", this.node.data, this.node);
		},
		collapse() {
			this.expanded = false;
			this.node.expanded = false;
			this.tree.emit("node-collapse", this.node.data, this.node);
		},
		selectNode() {
			if (this.node.disabled) return;
			this.tree.store.setCurrentNode(this.node);
			this.$emit("node-select", this.node);
			this.tree.emit("node-select", this.node.data, this.node);
		},
		handleChildNodeExpand(data, node) {
			this.$emit("node-expand", data, node);
		},
		handleChildNodeSelect(node) {
			this.$emit("node-select", node);
		},
	},
};
</script>

<style scoped>
.courseTree-node {
	width: 100%;
}

.courseTree-node__content {
	display: flex;
	align-items: center;
	height: 44px;
	line-height: 44px;
}

.courseTree-node__expand-icon {
	margin-right: 8px;
	display: inline-flex;
	align-items: center;
	transition: transform 0.3s;
	cursor: pointer;
}

.courseTree-node__expand-icon.expanded {
	transform: rotate(90deg);
}

.courseTree-node__loading-icon {
	margin-right: 8px;
	display: inline-flex;
	align-items: center;
}

.courseTree-node__label {
	flex: 1;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	padding-left: 5px;
	border-bottom: 1px solid #F5F5F5;
}

.courseTree-node__children {
	overflow: hidden;
}

.is-current .courseTree-node__label {
	color: #409eff;
	font-weight: bold;
}

.is-hidden {
	display: none;
}

.is-disabled {
	color: #ababab;
}
</style>
