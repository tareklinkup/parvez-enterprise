<style>
	.v-select{
		margin-bottom: 5px;
	}
	.v-select.open .dropdown-toggle{
		border-bottom: 1px solid #ccc;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
		height: 25px;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
	#products label{
		font-size:13px;
	}
	#products select{
		border-radius: 3px;
	}
	#products .add-button{
		padding: 2.5px;
		width: 28px;
		background-color: #298db4;
		display:block;
		text-align: center;
		color: white;
	}
	#products .add-button:hover{
		background-color: #41add6;
		color: white;
	}
</style>
<div id="products">
		<form @submit.prevent="saveProduct">
		<div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom: 15px;">
			<div class="col-md-6 col-md-offset-3">
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Date:</label>
					<div class="col-md-7">
						<input type="date" class="form-control" v-model="product.update_date">
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Category:</label>
					<div class="col-md-7">
						<v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/category" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Price (%):</label>
					<div class="col-md-7">
						<input type="number" step="0.01" class="form-control" v-model="product.update_percent" required>
					</div>
				</div>
			</div>	
            <div class="form-group clearfix">
                <div class="col-md-4 col-md-offset-7">
                    <input type="submit" class="btn btn-success btn-sm" value="Save">
                </div>
            </div>
		</div>
		</form>

		<div class="row">
			<div class="col-sm-12 form-inline">
				<div class="form-group">
					<label for="filter" class="sr-only">Filter</label>
					<input type="text" class="form-control" v-model="filter" placeholder="Filter">
				</div>
			</div>
			<div class="col-md-12">
				<div class="table-responsive">
					<datatable :columns="columns" :data="products" :filter-by="filter">
						<template scope="{ row }">
							<tr>
								<td>{{ row.update_date }}</td>
								<td>{{ row.ProductCategory_Name }}</td>
								<td>{{ row.update_percent }}</td>
								<td>{{ row.add_by }}</td>
								<td>{{ row.Brunch_name }}</td>
							</tr>
						</template>
					</datatable>
					<datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
				</div>
			</div>
		</div>


</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#products',
		data(){
			return {
				product: {
					update_date: moment().format('YYYY-MM-DD'),
					category_id: '',
					update_percent: '',
				},
				products: [],
				categories: [],
				selectedCategory: null,

				columns: [
                    { label: 'Date', field: 'update_date', align: 'center' },
                    { label: 'Category', field: 'ProductCategory_Name', align: 'center' },
                    { label: 'Price (%)', field: 'update_percent', align: 'center' },
                    { label: 'Add By', field: 'add_by', align: 'center' },
                    { label: 'Branch', field: 'Brunch_name', align: 'center' },
                ],
                page: 1,
                per_page: 10,
                filter: ''
			}
		},
		created(){
			this.getCategories();
			this.getProducts();
		},
		methods:{
			getCategories(){
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			
			getProducts(){
				axios.get('/get_category_price_updates').then(res => {
					this.products = res.data;
				})
			},
			saveProduct(){
				if(this.selectedCategory == null){
					alert('Select category');
					return;
				}
				if(this.product.update_percent == '' || isNaN(this.product.update_percent)){
					alert('Invalid Price');
					return;
				}

				this.product.category_id = this.selectedCategory.ProductCategory_SlNo;

				let url = '/add_category_price_update';
				axios.post(url, this.product)
				.then(res=>{
					let r = res.data;
					alert(r.message);
					if(r.success){
						this.clearForm();
						this.getProducts();
					}
				})
				
			},
			
			clearForm(){
				this.product = {
					update_date: moment().format('YYYY-MM-DD'),
					category_id: '',
					update_percent: '',
				}

                this.selectedCategory = null;
			}
		}
	})
</script>