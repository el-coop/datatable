<template>
	<modal :name="`datatable-row${name}`" :adaptive="true" height="100%" :pivotY="0"
		   :pivotX="1" :width="width">
		<div class="modal-body box">
			<button class="modal-close is-large" @click="hide"></button>
			<slot></slot>
		</div>
	</modal>
</template>

<script>
	export default {
		name: "DatatableRowDisplay",
		props: {
			name: {
				default: ''
			},
			width: {
				default: 600
			}
		},
		methods: {
			hide() {
				this.$modal.hide(`datatable-row${this.name}`);
			}
		}
	}
</script>

<style scoped lang="scss">
	$above-phone: "only screen and (min-width : 576px)";
	$above-tablet: "only screen and (min-width : #{$tablet})";
	$above-desktop: "only screen and (min-width : #{$desktop})";
	$above-wide-screen: "only screen and (min-width : #{$widescreen})";

	.modal-body {
		height: 100vh;
		overflow-x: hidden;
		overflow-y: auto;

		> *:last-child {
			margin-bottom: 1rem;
		}

		.modal-close {
			position: relative;
			float: right;
			top: 0;
			right: 0;

			&:after, &:before {
				background: $contrast-bg;
			}
		}
	}
</style>
