<template>
	<div>
		<div class="box" v-if="(!! $scopedSlots.buttons) || exportButton">
			<div class="field is-grouped">
				<div class="buttons">
					<slot name="buttons" :actions="buttonActions"></slot>
					<a v-if="exportButton" :href="`${this.url}/export?${exportOptions}`" class="button is-dark"
					   v-text="$translations.datatable.download || 'download'"></a>
				</div>
			</div>
		</div>
		<div class="table-wrapper">
			<div class="table-parent">
				<div class="table-container">
					<vuetable ref="table"
							  pagination-path=""
							  :api-url="`${url}/list`"
							  :fields="fields"
							  :css="css"
							  :append-params="params"
							  :per-page="perPage"
							  :no-data-template="$translations.datatable.noData || 'No data available'"
							  @vuetable:cell-clicked="cellClicked"
							  @vuetable:row-clicked="rowClicked"
							  @vuetable:pagination-data="paginationData"
							  @vuetable:loading='tableLoading'
							  @vuetable:loaded='tableLoaded'>
						<template :v-if="delete" #delete="props">
							<slot name="delete" :refresh="refresh" :props="props"></slot>
						</template>
					</vuetable>
				</div>
				<div class="level">
					<div class="level-left">
						<VuetablePaginationInfo class="level-item" ref="paginationInfo"
												:info-template="labels.pagination"
												:no-data-template="labels.noPagination">
						</VuetablePaginationInfo>
					</div>
					<div class="level-right">
						<VuetablePagination ref="pagination" class="level-item" :prev-text="labels.prev"
											:next-text="labels.next"
											@vuetable-pagination:change-page="changePage"></VuetablePagination>
					</div>
				</div>
			</div>
			<div class="filter">
				<DatatableFilter :table-fields="fields" @filter="filter" :filter-text="labels.filter"
								 :filters-text="labels.filters" :clear-text="labels.clear"
								 :init-filters="initFilters"></DatatableFilter>
			</div>
		</div>
		<DatatableRowDisplay :width="editWidth" :name="_uid" v-if="edit">
			<slot :object="object" :on-update="updateObject" :on-delete="deleteObject"></slot>
		</DatatableRowDisplay>
	</div>
</template>

<script>
	import Vuetable from 'vuetable-2/src/components/Vuetable.vue';
	import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo.vue';
	import VuetablePagination from './DatatablePagination';
	import DatatableFilter from './DatatableFilter';
	import DatatableFormatters from './DatatableFormatters';
	import DatatableRowDisplay from "./DatatableRowDisplay";


	export default {
		name: 'Datatable',
		mixins: [DatatableFormatters],
		components: {
			DatatableRowDisplay,
			DatatableFilter,
			Vuetable,
			VuetablePaginationInfo,
			VuetablePagination,
		},
		props: {
			url: {
				required: true,
				type: String
			},
			fieldSettings: {
				type: Array,
				default() {
					return [];
				}
			},
			perPageOptions: {
				type: Array,
				default() {
					return [10, 20, 50, 100];
				}
			},
			extraParams: {
				type: Object,
				default() {
					return {};
				}
			},
			formattersData: {
				type: Object,
			},


			labels: {
				type: Object,
				required: true
			},

			initFilters: {
				type: Object,
				default() {
					return {};
				}
			},

			editWidth: {
				default: 600
			},
			exportButton: {
				type: Boolean,
				default: true
			},
			delete: {
				type: Boolean,
				default: false
			},
			edit: {
				type: Boolean,
				default: true
			},
		},

		data() {
			return {
				fields: [],
				loading: false,
				tableCss: 'table is-bordered is-striped is-fullwidth',
				css: {
					tableClass: this.tableCss,
					ascendingClass: 'column-sorted column-sorted-up',
					descendingClass: 'column-sorted column-sorted-down',
				},
				perPage: 20,
				params: this.extraParams,
				object: null,
				exportOptions: '',
				buttonActions: {
					newObjectForm: this.newObjectForm
				},
			}
		},

		created() {
			this.fields = this.calcFields(this.fieldSettings);
			this.params.filter = this.initFilters;
		},

		methods: {
			calcFields(settings) {
				if (this.delete) {
					settings.push({
						name: '__slot:delete',
						title: '',
						filter: false
					})
				}

				return settings;
			},

			newObjectForm() {
				this.$modal.show(`datatable-row${this._uid}`);
				this.object = {};
			},

			paginationData(paginationData) {
				this.$refs.pagination.setPaginationData(paginationData);
				this.$refs.paginationInfo.setPaginationData(paginationData);
			},
			tableLoading() {
				this.css.tableClass = `${this.tableCss} is-loading`;
			},
			tableLoaded() {
				this.css.tableClass = this.tableCss;

				const name = window.location.pathname.split('/').slice(-1).pop();
				this.exportOptions = `name=${name}`;
				const params = this.$refs.table.httpOptions.params;
				for (let option in params) {
					let paramValue = params[option];
					if (typeof paramValue !== 'string') {
						paramValue = JSON.stringify(paramValue);
					}
					this.exportOptions += `&${option}=${paramValue}`;
				}
			},
			changePage(page) {
				this.$refs.table.changePage(page);
			},
			filter(filters) {
				this.params.filter = filters;
				Vue.nextTick(() => {
					this.$refs.table.refresh()
				})
			},
			cellClicked(data, field, event) {
				if (this.edit) {
					this.$modal.show(`datatable-row${this._uid}`);
				}
				this.object = data;
				this.$bus.$emit('vuetable-cell-clicked', {
					data, event
				});

			},
			rowClicked(data, event) {
				this.$bus.$emit('vuetable-row-clicked', {
					data, event
				});
			},
			updateObject(data) {
				this.object = {...this.object, ...data};
				const currentData = this.$refs.table.tableData;
				const elementIndex = currentData.findIndex((row) => {
					return row.id === data.id;
				});
				if (elementIndex > -1) {
					currentData[elementIndex] = this.object;
				} else {
					currentData.push(this.object);
					this.$modal.hide(`datatable-row${this._uid}`);
				}
				this.$refs.table.setData(currentData);
			},
			deleteObject(data) {
				this.$modal.hide(`datatable-row${this._uid}`);
				this.object = {...this.object, ...data};
				const currentData = this.$refs.table.tableData;
				let objectIndex = currentData.findIndex((item) => {
					return item.id === this.object.id;
				});
				currentData.splice(objectIndex, 1);
				this.$refs.table.setData(currentData);
			},
			refresh() {
				this.$refs.table.refresh()
			}
		},

	}
</script>

<style lang="scss">
	@import "variables";

	.table.is-loading {
		opacity: 0.4;
		position: relative;
		transition: opacity .3s ease-in-out;
	}

	.table.is-loading:after {
		position: absolute;
		content: '';
		top: 40%;
		left: 50%;
		margin: -30px 0 0 -30px;
		border-radius: 100%;
		animation-fill-mode: both;
		border: 4px solid #000;
		height: 60px;
		width: 60px;
		background: transparent !important;
		display: inline-block;
		animation: pulse 1s 0s ease-in-out infinite;
	}

	@keyframes pulse {
		0% {
			-webkit-transform: scale(0.6);
			transform: scale(0.6);
		}
		50% {
			-webkit-transform: scale(1);
			transform: scale(1);
			border-width: 12px;
		}
		100% {
			-webkit-transform: scale(0.6);
			transform: scale(0.6);
		}
	}

	.column-sorted:after {
		float: right;
	}

	.column-sorted.column-sorted-down:after {
		content: "\25bc"
	}

	.column-sorted.column-sorted-up:after {
		content: '\25b2'
	}

	.table-wrapper {
		display: flex;
		flex-direction: column;

		> .table-parent {
			flex: 1;
			margin-top: 1rem;

			> .table-container {
				max-width: calc(100vw - 2.5rem);
			}

			@media #{$above-tablet} {
				margin-top: 0;
				margin-right: 1rem;
			}

		}

		@media #{$above-tablet} {
			flex-direction: row;
		}

	}
</style>
