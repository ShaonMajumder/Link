# Links - React,Redux,Sanctum
---
author: Shaon Majumder
date: 18-10-2022
---

## Features
### User Features
- Authentication
- Data Vizualization
- CRUD (Unimplemented)
- Search (Unimplemented)

### User Features
- Create, Edit, Delete

### Dev Features
- Authentication (Bearer Token Based, Context Based)
- CRUD (Redux Based)
- Structered API Axios
- Redux tool kit query (rtk-query)
    - fetching data rtk-query, Redux createApi endpoints
    - Passing parameters in RTK query
    - set Header Conditionally by passing hookstate by getState
    - rtk-query pagination
    - conditional fetching data rtk-query
- React Design
    - Pagination Custom Component
    - React-Bootstrap 
        - Table
        - Responsive Breakpoint Specific
        - Pagination
        - Button

    - How to store the result of query from createApi in a slice


    change classname conditionally react
- Redux - slice and createApi combined for data deletation

### API Features
Customized Error Handling = 404
HTTP_METHOD_NOT_ALLOWED = 405;


* conditional fetching createApi( react-redux 

### Next Features
private & protected routes

## Technologies Used
- Laravel
    * Sanctum
- React
- Redux

## Quick Run
open bash
```bash
cd auth-api
php artisan serve
```
open another bash
```bash
cd auth-client
yarn install
yarn start
```

## Dependencies
```bash
yarn add react-redux
yarn add @reduxjs/toolkit
yarn add react-bootstrap bootstrap
yarn add react-router-dom
yarn add sweetalert2
```

Adding Icons
```bash
yarn add react-icons
```
Add sass
```bash
yarn add sass
```

## Links
Create Symbolic link
php artisan storage:link

## Backend 
```bash
composer create-project laravel/laravel auth-api
cd auth-api
composer require laravel/sanctum
code .
```
Edit app/Http/Kernel.php :
```php
'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
```

Edit config/cors.php :
```php
'allowed_origins' => [env('FRONTEND_URL','http://localhost:3000')], //['*']
'supports_credentials' => true, //false
```

database/factories/UserFactory :
```php
​ 'password' => bcrypt('12345678'), // '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
```

Edit database/seeders/DatabaseSeeder.php :
```php
public function run(){
        User::insert([
            'name' => 'Global Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'),        
            'remember_token' => Str::random(10),
        ]);   
        User::factory(10)->create();
    }

​ }
```
Generate Environment File
```bash
cp .env.example .env
```

Edit .env
```env
FRONTEND_URL=http://localhost:3000
DB_DATABASE=auth
DB_USERNAME=root
DB_PASSWORD=12345678
SESSION_DRIVER=cookie
SESSION_DOMAIN='.localhost'
SANCTUM_STATEFUL_DOMAINS='localhost,127.0.0.1'
```

Prepare Serve
```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

to kill a specified port process - 
ubuntu
```bash
sudo fuser -k port_number/tcp
```
<br>
<br>

Mac<br>
To get Pid -
```
netstat -vanp tcp | grep 3000
or
lsof -i tcp:3000
```
To kill with pid
```
kill -9 <pid>
```

### Errors
if you use autoload files helpers,
```php
"autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        },
        "files": [
            "app/Helpers/helpers.php"
        ]
    },
```
then run on backend:
composer dump-autoload

## FrontEnd
```bash
git clone https://github.com/unlikenesses/sanctum-react-spa 
mv sanctum-react-spa auth-client
cd auth-client
```

Run Application
```bash
yarn install
yarn start
```

set axio header for global :
```javascript
headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Credentials": "true",
        "Access-Control-Allow-Origin" : "http://localhost:3000"
    }
```

### accessing store from reducer inside slice
```javascript
import { current } from "@reduxjs/toolkit";
(state, payload ) => {
    const { linkItems } = current(state)
    // acessing in two ways
    console.log('Endpoint Hook listeners => state',current(state)['linkItems'],linkItems)
}
```
### accessing store 2
```javascript
import store from '../store'
console.log('Get State',store.getState().links.linkItems)
```
### accessing store 3
```javascript
import { useSelector } from "react-redux";
const { linkItems : linkItems2 } = useSelector((store) => store.links);
```
### Setting Store in Reducer
#### Method 1
```javascript
return {
    ...state,
    linkItems : payload.payload.data.links.data,
    total : payload.payload.data.links.total,
    per_page : payload.payload.data.links.per_page,
    current_page : payload.payload.data.links.current_page,
    last_page : payload.payload.data.links.last_page
}
```

#### Method 2
```javascript
    //setting responsed data to store by api endpoints rtk-query listener
    state.linkItems = [...payload.payload.data.links.data,1] ;
    state.total = payload.payload.data.links.total
    state.per_page = payload.payload.data.links.per_page
    state.current_page = payload.payload.data.links.current_page
    state.last_page = payload.payload.data.links.last_page
```

#### Method 3 - safest way
```javascript
    this.setState({
        linkItems : payload.payload.data.links.data,
        total : payload.payload.data.links.total,
        per_page : payload.payload.data.links.per_page,
        current_page : payload.payload.data.links.current_page,
        last_page : payload.payload.data.links.last_page
    })
```
### Immutability
```javascript
state.linkItems = linkItems.filter(link => link.id !== linkId) // immutable also, makes a copy as data is different
```
```javascript
return {
    ...state,
    linkItems : payload.payload.data.links.data
}
```
```javascript
 this.setState({
    linkItems : payload.payload.data.links.data
})
```

### linkSlice.js:81 Uncaught (in promise) TypeError: Cannot read properties of undefined (reading 'setState')
```javascript
  extraReducers: (builder) => {
    
    builder
    .addMatcher(
      isAllOf(linksApi.endpoints.links.matchFulfilled),
      (state, payload ) => {
        console.log('createApi -> extraReducers -> Links Index Listener, state and payload',state,payload)
        //setting responsed data to store by api endpoints rtk-query listener
        this.setState({
          linkItems : payload.payload.data.links.data,
          total : payload.payload.data.links.total,
          per_page : payload.payload.data.links.per_page,
          current_page : payload.payload.data.links.current_page,
          last_page : payload.payload.data.links.last_page
        })
      }
    )

    // Doesnt work
```
### Refetching in Redux
- https://stackoverflow.com/questions/72877343/accessing-rtk-query-data-state-across-multiple-components <br>
If you call the same useQuery hook with the same arguments in another component, those two will share the cache entry and return exactly the same data - it will not trigger another request to the server.


### No reducer provided for key "links"
import store above component file - import store from '../store'

### Error Handling
- if ERR_OSSL_EVP_UNSUPPORTED Error :
Go to package.json and change
`react-scripts start`
To
`react-scripts --openssl-legacy-provider start`

- If failed  GET https://localhost:8000/sanctum/csrf-cookie net::ERR_CONNECTION_CLOSED
Check laravel console log, if Invalid request (Unsupported SSL request)
Change 
https://127.0.0.1:8000
To
http://127.0.0.1:8000

- Login Route Cors Error :
if endpoint is http://localhost:8000/login
It is heating on web route, to get api route
change  http://localhost:8000/api/login

- If your application sends 200 for success, Change 204 to 200 for success status code

- Logout Route Cors Error :
if endpoint is http://localhost:8000/logout
It is heating on web route, to get api route
change  http://localhost:8000/api/logout

- For Logged in or auth:sanctum route :
check urls have api/endpoint; Set headers for every authorized url :

apiClient.post('api/logout',[],{
      headers : {
        'Authorization' : `Bearer ${Cookies.get('access_token')}`
      }
    })
- For reading cookie properly -
yarn add js-cookie



### Research
#### SCSS
parameters - https://sass-lang.com/documentation/style-rules/declarations
#### Laravel
PUT request sends empty payload from postman - https://stackoverflow.com/questions/40211452/no-body-data-found-in-laravel-5-3-with-put-request
    - Solution : send post request and in payload send {'_method': 'PUT'}

- Using HTTP Methods for RESTful Services - https://www.restapitutorial.com/lessons/httpmethods.html#:~:text=The%20primary%20or%20most%2Dcommonly,but%20are%20utilized%20less%20frequently.
- React Context State Management
https://www.freecodecamp.org/news/react-context-for-beginners/
- Redux Data Management
- React UseAuth Hook
https://hooks.reactivers.com/use-auth

- createApi react redux
    * https://redux-toolkit.js.org/rtk-query/api/createApi<br>
    * https://blog.openreplay.com/fetching-data-in-redux-using-rtk-query/<br>
    * https://redux-toolkit.js.org/rtk-query/usage/queries#frequently-used-query-hook-return-values
    * https://redux-toolkit.js.org/rtk-query/usage/pagination
    * Passing parameters in RTK query - https://stackoverflow.com/questions/69499487/rtk-query-pagination-and-combine-queries
    * set Header Conditionally by passing hookstate by getState - https://redux-toolkit.js.org/rtk-query/api/fetchBaseQuery
    * conditional fetching createApi( react-redux - https://redux-toolkit.js.org/rtk-query/usage/conditional-fetching
    * useSelector, useContext, createContext
- change classname conditionally react - https://www.pluralsight.com/guides/applying-classes-conditionally-react
- Passing State of Parent to Child Component as Props - https://www.pluralsight.com/guides/passing-state-of-parent-to-child-component-as-props
- Javascript Extracting Variables From Arrays And Objects With Object Destruction - https://webmobtuts.com/javascript/javascript-extracting-variables-from-arrays-and-objects-with-object-destruction/
- display none react - https://stackoverflow.com/questions/37728951/how-to-css-displaynone-within-conditional-with-react-jsx
- acess hooks - https://egghead.io/lessons/react-access-state-with-the-redux-useselector-hook
- How to Pass Data between React Components - https://www.pluralsight.com/guides/how-to-pass-data-between-react-components
- handling events - https://reactjs.org/docs/handling-events.html
- RTK extra reducers - https://redux-toolkit.js.org/rtk-query/usage/examples#using-extrareducers
- RTK React Prefers Immutability - https://daveceddia.com/react-redux-immutability-guide/
- RTK Why Not To Modify React State Directly - https://daveceddia.com/why-not-modify-react-state-directly/
- RTK shallowEqual
    - https://stackoverflow.com/questions/58212159/strict-equality-versus-shallow-equality-checks-in-react-redux
    - https://react-redux.js.org/api/hooks
- RTK Force Refetching with hooks useQuerySubscription - https://redux-toolkit.js.org/rtk-query/api/created-api/hooks#usequerysubscription    
- RTK force a refetch, .initiate(), Observing caching behavior, Avoiding unnecessary requests, Selecting data from a query result,  - https://redux-toolkit.js.org/rtk-query/usage/queries
- RTK providedTags does not work on createApi - https://stackoverflow.com/questions/72175487/rtk-query-always-returns-cached-data-invalidatestags-not-doing-anything
- Delete will auto refetch useQuery with Tags invalidation, data no manual manipulation needed -https://redux-toolkit.js.org/rtk-query/usage/pagination#automated-re-fetching-of-paginated-queries
- On Update will auto refetch specific row useQuery with Tags invalidation, data no manual manipulation needed - https://redux-toolkit.js.org/rtk-query/usage/automated-refetching
- Why is my React component is rendering twice?
    - https://stackoverflow.com/questions/48846289/why-is-my-react-component-is-rendering-twice
    - https://reactjs.org/docs/strict-mode.html#detecting-unexpected-side-effects
- How does React.useState triggers re-render? - https://stackoverflow.com/questions/53024488/how-does-react-usestate-triggers-re-render
- React-Bootstrap
    - React-bootstrap Table - https://react-bootstrap.github.io/components/table/
    - React-bootstrap Pagination - https://react-bootstrap.github.io/components/pagination/
    - React-bootstrap Button - https://react-bootstrap.github.io/components/buttons/

- React Syllabus
    * mutation and immutaion
    * arrow and defined function
    * component lifecycle
    * state lifecycle
    * variable and state variable
    * state behavior / variable behavior with virtual DOM
    * Event Loop
    * Hooks
    * Redux
- Laravel API returns a view 404 error instead of JSON error - https://stackoverflow.com/questions/56401115/laravel-api-returns-a-view-404-error-instead-of-json-error
Next To Learn
    - RTK Fetching data Store - https://blog.openreplay.com/fetching-data-in-redux-using-rtk-query/
    - https://redux.js.org/tutorials/essentials/part-8-rtk-query-advanced#selecting-values-from-results
    - https://redux-toolkit.js.org/rtk-query/usage/prefetching
    - https://redux-toolkit.js.org/rtk-query/usage/automated-refetching
    - https://redux-toolkit.js.org/rtk-query/api/created-api/hooks#uselazyquery
    - https://redux.js.org/tutorials/essentials/part-8-rtk-query-advanced#selecting-values-from-results
    
    - Higher Order Component
    - placing pagination buttons
    - https://stackoverflow.com/questions/68612556/how-to-store-the-result-of-query-from-createapi-in-a-slice

    - https://redux.js.org/tutorials/essentials/part-8-rtk-query-advanced
    - https://reactjs.org/docs/hooks-custom.html
    - https://react-redux.js.org/api/hooks
    
    - https://codeburst.io/global-state-with-react-hooks-and-context-api-87019cc4f2cf
    - https://www.thisdot.co/blog/creating-a-global-state-with-react-hooks
    - https://endertech.com/blog/using-reacts-context-api-for-global-state-management
    - https://www.basefactor.com/global-state-with-react
    - https://stackoverflow.com/questions/69675357/react-what-is-the-proper-way-to-do-global-state
    - global state outside component react
    - https://reactjs.org/docs/hooks-state.html
    - https://redux.js.org/tutorials/fundamentals/part-6-async-logic
