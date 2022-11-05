import { configureStore } from "@reduxjs/toolkit";
import linkReducer from "./reducers/linkSlice";
import { linksApi } from "./services/api";
import React from "react";

const initialState = {
  loggedInState : false
}
export const AuthContext = React.createContext(initialState);
// const [loggedInState, setloggedInState] = useState(false);
// export const GlobalContext = React.useState(false);
// export const GlobalContext = createContext(initialState);

const store = configureStore({
  reducer: {
    links: linkReducer,
    [linksApi.reducerPath]: linksApi.reducer,
  },
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware().concat(linksApi.middleware)
});

export default store;