import axios from 'axios';
import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'
import Cookies from 'js-cookie';
import { useDispatch } from "react-redux";

export const api_url = process.env.REACT_APP_API_URL || "http://localhost:8000"
export const client_url = process.env.REACT_APP_CLIENT_URL || "http://localhost:3000"
export const login_url = process.env.REACT_APP_LOGIN_URL || "api/login"
export const logout_url = process.env.REACT_APP_LOGOUT_URL || "api/logout"
export const csrf_token_url = process.env.REACT_APP_CSRF_TOKEN_URL || "/sanctum/csrf-cookie"
export const link_url = process.env.REACT_APP_LINK_URL || "/api/links"
export const get_link_url = process.env.REACT_APP_LINK_GET_URL || "/api/links"
export const link_create_url = process.env.REACT_APP_LINK_CREATE_URL || "/api/links/add"
export const link_delete_url = process.env.REACT_APP_LINK_DELETE_URL || "/api/links/delete"
export const link_get_tags_url = process.env.REACT_APP_LINK_GET_TAGS_URL || "/api/links/tags"

function providesList(resultsWithIds, tagType) {
    return resultsWithIds
      ? [
          { type: tagType, id: 'LIST' },
          ...resultsWithIds.data.links.data.map(({ id }) => ({ type: tagType, id })),
        ]
      : [{ type: tagType, id: 'LIST' }]
}

// Define a service using a base URL and expected endpoints
export const linksApi = createApi({
    reducerPath: "linksApi",
    baseQuery: fetchBaseQuery({
        baseUrl: `${api_url}/api`,
        prepareHeaders: (headers, { getState }) => {
            // const isLoggedIn = getState().links.isLoggedIn
            const isLoggedIn = sessionStorage.getItem('loggedIn') === 'true' || false
            headers.set('Access-Control-Allow-Origin', client_url)
            headers.set('Content-Type', 'application/json')
            headers.set('Accept', 'application/json')
            headers.set('Access-Control-Allow-Credentials', 'true')

            // test result to turn on providedTags caching
            headers.set('Cache-Control', 'no-cache');
            headers.set('Pragma', 'no-cache');
            headers.set('Expires', '0');
            // test result to turn on providedTags caching
            
            if (isLoggedIn) {
                headers.set('Authorization', `Bearer ${Cookies.get('access_token')}`)
            }
            return headers
        }
    }),
    tagTypes: ['Link', 'User'],
    endpoints: (builder) => ({
        links: builder.query({
            query: (page = 1) => {
                // console.log("OK");
                return `/links?page=${page}`;
            },
            providesTags: (result) => providesList(result, 'Link'),
            // providesTags: ['Link'],
            // providesTags: (result, error, page) => 
            //     result
            //     ? [
            //         // Provides a tag for each post in the current page,
            //         // as well as the 'PARTIAL-LIST' tag.
            //         ...result.data.links.data.map(({ id }) => ({ type: 'Link', id })),
            //         { type: 'Link', id: 'PARTIAL-LIST' },
            //         ]
            //     : [{ type: 'Link', id: 'PARTIAL-LIST' }],


        }),
        getTags: builder.query({
            query: () => {
                return `/links/tags`;
            },
            // providesTags: (result) => providesList(result, 'Tag'),
        }),
        addLink: builder.mutation({
            query: (link) => ({
                url : '/links/add',
                method: "POST",
                body: link
            }),
            transformResponse: (response, meta, arg) => response,
            
            // invalidatesTags: (result, error, id) => [
            //     { type: 'Link', id },
            //     { type: 'Link', id: 'LIST' },
            //   ],
            //   invalidatesTags: [{ type: 'Link', id: 'LIST' },10],
            invalidatesTags: ['Link'], // after creation invalidatesTags, refetch to first page,try sending to last 
        }),
        updateLink: builder.mutation({
            query: (rest ) => ({
                url : `links/update/${rest.id}`,
                method : 'PUT',
                body : rest
            }),
            // transformResponse: (response, meta, arg) => response,
            transformResponse: (response, meta, arg) => {
                // console.log('deleteLink => transformResponse')
                return {
                    originalArg: arg,
                    data: response,
                }
            },
            invalidatesTags: (result, error, arg) => [{ type: 'Link', id: arg.id }], // done
        }),
        deleteLink: builder.mutation({
            query: (id) => ({
                url : `links/delete/${id}`,
                method: 'DELETE'
            }),
            invalidatesTags: (result, error, id) => [
                { type: 'Link', id },
                { type: 'Link', id: 'PARTIAL-LIST' },
              ], // done
        
            transformResponse: (response, meta, arg) => {
                // console.log('deleteLink => transformResponse')
                return {
                    originalArg: arg,
                    data: response,
                }
            },
            async onQueryStarted(
                arg,
                { dispatch, getState, queryFulfilled, requestId, extra, getCacheEntry }
            ) {
                
                // console.log('getState onQueryStarted',getState())
                // console.log('deleteLink => onQueryStarted, arg',arg)
                queryFulfilled.then(()=>{
                // console.log('deleteLink => onQueryStarted getState()',requestId,getState())
                
                })
            },
        })
    })
});

export const {
    endpoints,
    reducerPath, 
    reducer, 
    middleware,
    
    useLinksQuery,
    useGetTagsQuery,
    useAddLinkMutation,
    useDeleteLinkMutation,
    useUpdateLinkMutation
} = linksApi;



// IF axios.create not used, we set default config for axio, axios.defaults.withCredentials = true
var headers;
const isLoggedIn2 = sessionStorage.getItem('loggedIn') === 'true' || false
if(isLoggedIn2){
    headers = {
        "Content-Type": "application/json",
        "Access-Control-Allow-Credentials": 'true',
        "Access-Control-Allow-Origin" : client_url,
        "Authorization" : `Bearer ${Cookies.get('access_token')}`
    };
} else{
    headers = {
        "Content-Type": "application/json",
        "Access-Control-Allow-Credentials": 'true',
        "Access-Control-Allow-Origin" : client_url,
    };
}

const apiClient = axios.create({
    headers: headers,
    baseURL: api_url,
    withCredentials: true,
})

export default apiClient