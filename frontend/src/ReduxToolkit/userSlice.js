import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import { axiosInstance } from '../axios/axiosInstance';

// Create a thunk to fetch user profile data
export const getMe = createAsyncThunk(
  'user/getMe',
  async (_, { rejectWithValue }) => {
    try {
      const token = localStorage.getItem("token");

      // Check if token exists
      if (!token) {
        throw new Error("No token available");
      }

      // Add the token to the request headers
      const config = {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      };

      // Send request with token in headers
      const response = await axiosInstance.get("/auth/profile", config);
      console.log("user",response.data)
      return response.data.user;
    } catch (error) {
      return rejectWithValue(error.response.data);
    }
  }
);

// Define initial state
const initialState = {
  profile: null,
  loading: false,
  error: null,
};

// Define user slice
// Define user slice
const userSlice = createSlice({
    name: 'user',
    initialState,
    reducers: {
      setUser: (state, action) => {
        state.profile = action.payload;
      },
    },
    extraReducers: (builder) => {
      builder
        .addCase(getMe.fulfilled, (state, action) => {
          state.loading = false;
          state.error = null;
          state.profile = action.payload;
        })
        .addCase(getMe.rejected, (state, action) => {
          state.loading = false;
          state.error = action.payload;
        });
    },
  });
  
  
  // Export actions and reducer
  export const { setUser } = userSlice.actions;
  export default userSlice.reducer;
  