<template>
	<div />
</template>
<script>
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'FormExpired',
    props: {
		value: {
			type: String,
			default: '',
		},
		check: {
			type: Object,
			default: () => { return {} },
		},
	},
	setup() {},
})
</script>
