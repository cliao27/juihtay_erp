import CustomerPage from '../pages/Customer.vue';
// import CustomerList from '../components/CustomerList.vue';
// import CustomerDocument from '../components/CustomerDocument.vue';

import ProductPage from '../pages/Product.vue';
// import ProductList from '../components/ProductList.vue';
// import ProductDocument from '../components/ProductEssential.vue';

import OrderPage from '../pages/Order.vue';
// import OrderList from '../components/OrderList.vue';
// import OrderDocument from '../components/OrderDocument.vue';

import WorkPage from '../pages/Work.vue';
// import WorkList from '../components/WorkList.vue';
// import WorkDocument from '../components/WorkDocument.vue';

import MaterialPage from '../pages/Material.vue';
// import MaterialList from '../components/MaterialList.vue';
// import MaterialDocument from '../components/MaterialDocument.vue';


const Home = {
	template: '<div><h1>Home</h1></div>'
}
const Page404 = {
	template: '<div><h1>404</h1></div>'
}

const routes = [
	// {
	// 	path: '/product/customer/:id', 
	// 	name: 'ProductCustomerList',
	// 	component: ProductCustomerList
	// },
	{
		path: '/',
		component: Home
	},
	{
		path: '/customer',
		name: 'Customer',
		component: CustomerPage,
		children: [
			{
				path: ':id',
				name: 'CustomerDocument',
			}
		]
	},
	{
		path: '/product',
		name: "Product",
		component: ProductPage,
		children: [
			{
				path: 'customer/:id',
				name: 'ProductCustomerList',
			},
			{
				path: ':id',
				name: 'ProductDocument',
			}
		]
	},
	{
		path: '/order',
		name: "Order",
		component: OrderPage,
		children: [
			{
				path: 'customer/:id',
				name: 'OrderCustomerList',
			},
			{
				path: ':id',
				name: 'OrderDocument',
			}
		]
	},
	{
		path: '/work',
		name: "Work",
		component: WorkPage,
		children: [
			{
				path: 'customer/:id',
				name: 'WorkCustomerList',
			},
			{
				path: ':id',
				name: 'WorkDocument',
			}
		]
	},
	{
		path: '/material',
		name: "Material",
		component: MaterialPage,
		children: [
			{
				path: 'customer/:id',
				name: 'MaterialCustomerList',
			},
			{
				path: ':id',
				name: 'MaterialDocument',
			}
		]
	},
	{
		path: '*',
		component: Page404
	}
]

export default routes
