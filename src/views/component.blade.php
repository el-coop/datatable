<?php


if (!isset($fields)) {
	$fields = \ElCoop\Datatable\Services\DatatableService::getFields(\Request::path());
}

$filters = collect($filters ?? [])->merge(json_decode(\Request::get('filters', '[]')));
?>
<datatable :field-settings="{{ $fields }}"
	:extra-params="{
		table: '{{\Request::path()}}'
	}"
	@isset($editWidth)
		:edit-width="{{$editWidth}}"
	@endisset
	@if(isset($customUrl))
		url="{{$customUrl}}"
	@else
		url="\datatable"
	@endif
	:labels="{
		pagination: '@lang('datatable.pagination')',
		noPagination: '@lang('datatable.noPagination')',
		next: '@lang('datatable.next')',
		prev: '@lang('datatable.prev')',
		filters: '@lang('datatable.filters')',
		filter: '@lang('datatable.filter')',
		clear: '@lang('datatable.clear')',
	}"
	:init-filters="{{ $filters->count() ? $filters : '{}' }}"
	@isset($deleteButton)
		:delete-slot="true"
	@endif
	@isset($formattersData)
		:formatters-data="{{$formattersData}}"
	@endif
	@isset($deleteButtonTxt)
	   delete-btn="{{$deleteButtonTxt}}"
	@endisset
>
	@isset($buttons)
		<template #buttons="{actions}">{{$buttons}}</template>
	@endisset
	<template #default="{object, onUpdate}">
		<template v-if="object">
			{{ $slot }}
		</template>
	</template>
</datatable>
