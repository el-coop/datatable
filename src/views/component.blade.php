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
		   @isset($delete)
		   :delete="true"
		   @endisset
		   @isset($edit)
		   :edit="{{$edit}}"
		   @endisset
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

		   @isset($formattersData)
		   :formatters-data="{{$formattersData}}"
		@endif
>
	@isset($buttons)
		<template #buttons="{actions}">{{$buttons}}</template>
	@endisset
	@isset($delete)
		<template #delete="{refresh,props}">{{$delete}}</template>
	@endisset
	<template #default="{object, onUpdate}">
		<template v-if="object">
			{{ $slot }}
		</template>
	</template>
</datatable>
