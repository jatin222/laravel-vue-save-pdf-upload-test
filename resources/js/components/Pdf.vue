<template>
	
	<div id="pageContainer">
	</div>
	
</template>

<script>
	import * as pdfjsLib from "pdfjs-dist";
	import { PDFViewer } from "pdfjs-dist/web/pdf_viewer";
	import "pdfjs-dist/web/pdf_viewer.css";

	pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdn.jsdelivr.net/npm/pdfjs-dist@2.13.216/build/pdf.worker.min.js";

    export default {
    	name: 'Home',
    	data() {
    		return {
    			container: null
    		}
    	},
        mounted() {
        	this.container = document.getElementById("pageContainer")
        	if(this.$store.state.current_file && this.$store.state.current_file.path)
        	{
        		this.getPdf()
        	}
        },
        methods: {
        	async getPdf() {
        		try {
        			document.getElementById('pageContainer').innerHTML = ''
        			let url = this.$store.state.current_file.path
        			let container = this.container;
					let loadingTask = pdfjsLib.getDocument("./"+url);

					let pdf = await loadingTask.promise;

					for(let i = 1; i <= pdf.numPages; i++)
					{
						this.addPdfPageToView(pdf, i)
					}
        		} catch(e) {
        			// statements
        			console.log(e);
        		}
					
			},
			async addPdfPageToView(pdf, page_number) {

				try {
					// Fetch the first page
				    const page = await pdf.getPage(page_number)
				    const scale = 1.5;
				    const viewport = page.getViewport({ scale });

				    // Support HiDPI-screens.
	    			const outputScale = window.devicePixelRatio || 1;

	    			// Prepare canvas using PDF page dimensions
	    			const canvas = document.createElement("canvas")
	    			const context = canvas.getContext("2d")
	    			canvas.width = Math.floor(viewport.width * outputScale)
				    canvas.height = Math.floor(viewport.height * outputScale);
				    canvas.style.width = Math.floor(viewport.width) + "px";
				    canvas.style.height = Math.floor(viewport.height) + "px";

				    const transform = outputScale !== 1 
	      				? [outputScale, 0, 0, outputScale, 0, 0] 
	      				: null;

	      			this.container.appendChild(canvas)

	      			// Render PDF page into canvas context
	      			const renderContext = {
				      canvasContext: context,
				      transform,
				      viewport,
				    };
				    page.render(renderContext);
					//this.container.textContent = pdf
				} catch(e) {
					// statements
					console.log(e);
				}
				    
			}
        },
        watch: {
          '$store.state.current_file.path': function(val, oldVal) {
          	this.getPdf()
          }
        }
    }
</script>