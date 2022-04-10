<template>
	<div class="left-rectangle">

		<div class="left-top">
			<div class="files-text">
				FILES
			</div>
			<div class="upload-text cursor" @click="uploadFile()">
				Upload 
				<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M10.2773 3.9668C10.4258 4.11523 10.5 4.29297 10.5 4.5C10.5 4.71875 10.4258 4.89844 10.2773 5.03906C10.1328 5.17969 9.95703 5.25 9.75 5.25H7.875V9C7.875 9.20703 7.80078 9.38477 7.65234 9.5332C7.50781 9.67773 7.33203 9.75 7.125 9.75H4.875C4.66797 9.75 4.49023 9.67773 4.3418 9.5332C4.19727 9.38477 4.125 9.20703 4.125 9V5.25H2.25C2.04297 5.25 1.86523 5.17969 1.7168 5.03906C1.57227 4.89844 1.5 4.71875 1.5 4.5C1.5 4.29688 1.57227 4.11914 1.7168 3.9668L5.4668 0.216797C5.61133 0.0722656 5.78906 0 6 0C6.20703 0 6.38281 0.0722656 6.52734 0.216797L10.2773 3.9668ZM10.5 10.5V7.5H12V10.5C12 10.9141 11.8535 11.2676 11.5605 11.5605C11.2676 11.8535 10.9141 12 10.5 12H1.5C1.08594 12 0.732422 11.8535 0.439453 11.5605C0.146484 11.2676 0 10.9141 0 10.5V7.5H1.5V10.5H10.5Z" fill="#9CA0B2"/>
				</svg>

			</div>
		</div>
		<div class="pdf-list-item">
			<pdffile v-if="files.length" v-for="(file, index) in files" :key="file.id" @click="selectFile(file)" :index="index" :file="file"></pdffile>
		</div>
	</div>
	<div class="right-rectangle">
		<div class="right-rectangle-top">
			<h4 class="right-pdf-title" v-if="$store.state.current_file">{{ $store.state.current_file.name }} #{{ getCurrentIndexNumber() }}</h4>
			<!-- <p ></p> -->
		</div>
		<pdf class="mt-5"></pdf>
	</div>



	<form class="d-none" ref="form" method="post"	>
		<input type="file" id="file_upload" @change="previewFiles" ref="file" v-if="show_file_input">
	</form>

	<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Enter tags</h5>
				</div>
				<div class="modal-body">
					<input type="text" class="form-control" v-model="tags">
					<small>Please enter , between each tag</small>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" @click="enterTags()">Save changes</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import Pdf from "./Pdf"
	import PdfFileListItem from "./PdfFileListItem"

    export default {
    	name: 'Home',
    	data() {
    		return {
    			showModel: false,
    			file_upload: null,
    			file: null,
    			show_file_input: true,
    			files: [],
    			tags: null,
    			file_upload_id: null,
    			myModal: null
    		}
    	},
    	components: {
    		pdf : Pdf,
    		pdffile: PdfFileListItem
    	},
        mounted() {
        	this.getSelectedFile()
        	this.getAllFiles()
        },
        methods: {
        	uploadFile() {
        		document.getElementById('file_upload').click()
        	},
        	previewFiles(event) {
        		let self = this
        		if(event.target.files.length)
        		{
        			this.file = this.$refs.file.files[0]

        			let formData = new FormData()
        			formData.append('file', this.file);

        			axios.post('/file-upload',
        				formData, {
                		headers: {
                    		'Content-Type': 'multipart/form-data'
                		}
                	})
        			.then(res => {
        				self.show_file_input = false
        				self.$nextTick(() => {
        				  self.show_file_input = true
        				})

        				if(res.data.status) {
        					self.$toast.success(`File uploaded successfully`)

        					self.files.push(res.data.data)
        					self.file_upload_id = res.data.data.id

        					self.selectFile(res.data.data)

        					//ask for tags
        					self.myModal = new bootstrap.Modal(document.getElementById('myModal'), { backdrop:true })
        					self.myModal.show()
        				}
        				else if(res.data.message == 'validation_error')
        				{
        					self.$toast.warning(`Please upload only .pdf file of size within 5MB.`)
        				}
        				else if(res.data.message == 'already_uploaded')
        				{
        					self.$toast.warning(`This file is already uploaded by you.`)
        				}
        				else
        				{
        					self.$toast.error(`Something went wrong, please contact admin.`)
        				}
        			})
        			.catch(err => {
        				self.show_file_input = false
        				self.$nextTick(() => {
        				  self.show_file_input = true
        				})

        				self.$toast.error(`Something went wrong, please contact admin.`)
        			})
        		}
			},
        	getAllFiles()
        	{
        		let self = this
        		axios.get('/get-all-files')
        		.then(res => {
        			if(res.data.status) 
        			{
        				self.files = res.data.data
        			}
        			else
        			{
        				self.$toast.error(`Something went wrong, please contact admin.`)
        			}
        		})
        		.catch(err => {
        			self.$toast.error(`Something went wrong, please contact admin.`)
        		})
        	},
        	enterTags() {
        		let self = this
        		if(self.tags.length)
        		axios.post('/add-tags', {
        			tags: self.tags,
        			file_upload_id: self.file_upload_id
        		})
        		.then(res => {
        			if(res.data.status)
        			{
        				self.$toast.success(`Tags added successfully.`)

        				for(let i=0; i< self.files.length; i++)
        				{
        					if(self.files[i].id == self.file_upload_id)
        					{
        						self.files[i] = res.data.data

        					}
        				}

        				self.myModal.hide()
        				self.tags = null
        				self.file_upload_id = null
        			}
        			else
        			{
        				self.$toast.error(`Something went wrong, please contact admin.`)
        			}
        		})
        		.catch(err => {
        			self.$toast.error(`Something went wrong, please contact admin.`)
        		})
        	},
        	
        	selectFile(file) {
        		this.$store.commit('changeCurrentFile', file)

        		axios.post('/select-file', {
        			file_id: file.id
        		})
        		.then(res => {
        		})
        		.catch(err => {
        			self.$toast.error(`Something went wrong, please contact admin.`)
        		})
        	},
        	getCurrentIndexNumber()
        	{
        		if(this.$store.state.current_file)
        		return this.files.findIndex(file => file.id == this.$store.state.current_file.id) + 1
        	},
        	getSelectedFile() {
        		let self = this
        		axios.post('/get-selected-file', {
        		})
        		.then(res => {
        			if(res.data.status)
        			{
        				self.selectFile(res.data.data)
        			}
        		})
        		.catch(err => {
        			self.$toast.error(`Something went wrong, please contact admin.`)
        		})
        	}
        }
    }
</script>