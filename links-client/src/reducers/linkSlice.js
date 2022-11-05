import { createSlice, createAsyncThunk, isAllOf, current } from "@reduxjs/toolkit";
import apiClient,{linksApi, link_url} from "../services/api";
import Cookies from "js-cookie";
import Swal from "sweetalert2";
import {useHistory} from 'react-router-dom';
import store from "../store";
import { useLinksQuery } from "../services/api";
const initialState = {
  linkItems: [],
  total: 0,
  per_page : 0,
  current_page : 1,
  last_page : 0,
  isLoggedIn: false,
  isLoading: true,
};


//Fetch
// export const getLinkItems = createAsyncThunk("link/getLinkItems", () => {
//   return fetch(url)
//     .then((resp) => resp.json())
//     .catch((err) => console.log(err));
// });

//Axios
// ThunkAPI can get the state of the APP and access andinvoke functions on the state
export const getLinkItems = createAsyncThunk(
  "link/getLinkItems",
  async (name, thunkAPI) => {
    try {
      apiClient.interceptors.request.use(config => {
        config.headers['Authorization'] = `Bearer ${Cookies.get('access_token')}`;
        return config;
      });
      const resp = await apiClient.get(link_url);
      return resp.data;
    } catch (error) {
      return thunkAPI.rejectWithValue("something went wrong");
    }
  }
);

const linkSlice = createSlice({
  name: "link",
  initialState,
  reducers: {
    clearLinkList: (state) => {
      state.linkItems = [];
    },
    removeItem: (state, action) => {
      const itemId = action.payload;
      state.linkItems = state.linkItems.filter((item) => item.id !== itemId);
    },
    setPageItem: (state,action) => {
      // state.current_page = action.payload
      return {
        ...state,
        current_page : action.payload
      }
      // console.log(useLinksQuery(action.payload))
    },
    setLoggedIn: (state) => {
      state.isLoggedIn = true
      sessionStorage.setItem('loggedIn',true)
      console.log('loggedIn',true)
    },
    setLoggedOut: (state) => {
      state.isLoggedIn = false
      sessionStorage.setItem('loggedIn',false)
    }
   
  },
  extraReducers: (builder) => {
    
    builder
    .addMatcher(
      isAllOf(linksApi.endpoints.links.matchFulfilled),
      (state, payload ) => {
        console.log('createApi -> extraReducers -> Links Index Listener, state and payload',state,payload)
        //setting responsed data to store by api endpoints rtk-query listener
        return {
          ...state,
          linkItems : payload.payload.data.links.data,
          total : payload.payload.data.links.total,
          per_page : payload.payload.data.links.per_page,
          current_page : payload.payload.data.links.current_page,
          last_page : payload.payload.data.links.last_page
        }
      }
    )
    .addMatcher(
      isAllOf(linksApi.endpoints.addLink.matchFulfilled),
      (state, payload ) => {
        const { linkItems, total } = current(state)
        // return {
        //   linkItems: linkItems.push(),
        //   total: total + 1
        // }
      }
    )
    .addMatcher(
      isAllOf(linksApi.endpoints.updateLink.matchFulfilled),
      (state, payload ) => {
        // const { linkItems, total } = current(state)
        // const id = payload.payload.originalArg.id
        // const payload_data = payload.payload.data.data
        
        // return {
        //   ...state,
        //   linkItems : linkItems.map((item, index) => {
        //     if( item.id == id){
        //       return payload_data
        //     }else{
        //       return item
        //     }
        //   })
        // }
        
      }
    )
    .addMatcher(
      isAllOf(linksApi.endpoints.deleteLink.matchFulfilled),
      (state, payload ) => {
        // let linkId = payload.payload.originalArg
        // console.log('Delete Listner',payload.payload.data)
        // const { linkItems, total } = current(state)
        // return {
        //   linkItems: linkItems.filter(link => link.id !== linkId),
        //   total: total - 1
        // }
      }
    )
    .addMatcher(
      isAllOf(linksApi.endpoints.deleteLink.matchRejected),
      (state, payload ) => {
        
      }
    )
  },
  // {
  //   [getLinkItems.pending]: (state) => {
  //     state.isLoading = true;
  //   },
  //   [getLinkItems.fulfilled]: (state, action) => {
  //     console.log(action);
  //     state.isLoading = false;
  //     state.linkItems = action.payload.data.links.data
  //   },
  //   [getLinkItems.rejected]: (state, action) => {
  //     console.log(action);
  //     state.isLoading = false;
  //   },
  // },
});





//console.log(linkSlice);
export const { clearLinkList, nextPage,removeItem, setLoggedIn, setLoggedOut, setPageItem } = linkSlice.actions;

export default linkSlice.reducer;
