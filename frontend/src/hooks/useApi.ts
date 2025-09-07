import { useState, useCallback } from 'react';
import { apiClient } from '@/lib/api-client';
import type { PostsResponse, CreatePostData, ApiResponse } from '@/types';

interface UseApiState<T> {
  data: T | null;
  loading: boolean;
  error: string | null;
}

interface UseApiReturn<T> extends UseApiState<T> {
  execute: (...args: unknown[]) => Promise<T | null>;
  reset: () => void;
}

export function useApi<T = unknown>(
  apiFunction: (...args: unknown[]) => Promise<T>
): UseApiReturn<T> {
  const [state, setState] = useState<UseApiState<T>>({
    data: null,
    loading: false,
    error: null,
  });

  const execute = useCallback(async (...args: unknown[]): Promise<T | null> => {
    setState(prev => ({ ...prev, loading: true, error: null }));
    
    try {
      const result = await apiFunction(...args);
      setState({ data: result, loading: false, error: null });
      return result;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'An error occurred';
      setState({ data: null, loading: false, error: errorMessage });
      return null;
    }
  }, [apiFunction]);

  const reset = useCallback(() => {
    setState({ data: null, loading: false, error: null });
  }, []);

  return {
    ...state,
    execute,
    reset,
  };
}

// 特定のAPI用のフック
export function usePosts() {
  return useApi<PostsResponse>((...args: unknown[]) => 
    apiClient.getPosts(args[0] as number | undefined, args[1] as number | undefined)
  );
}

export function useCreatePost() {
  return useApi<ApiResponse>((...args: unknown[]) => 
    apiClient.createPost(args[0] as CreatePostData)
  );
}

export function useUserProfile(userId: number) {
  return useApi<ApiResponse>(() => apiClient.getUserProfile(userId));
}
