<template>
	<div class="row mb-2 cursor">
	  <div class="col-sm-12">
	    <div class="card card-color" :class="{ 'card-active': $store.state.current_file && $store.state.current_file.id == file.id }">
	      <div class="card-body">
	        <h5 class="card-title">{{ file.name }} #{{ index + 1 }}</h5>
	        <p class="card-text">
	        	{{ getTags(file.tags) }}
	        </p>
	      </div>
	    </div>
	  </div>
	</div>
</template>

<script>
	export default {
		props: ['file', 'index'],

		methods: {
			getTags(tags) {
				if(!tags)
					return
        		let tag_arr = []
        		for(let i = 0; i < tags.length; i++)
        		{
        			tag_arr.push(tags[i].name.en)
        		}

        		return tag_arr.toString()
        	},
		}
	}
</script>
