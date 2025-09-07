import { useMemo } from 'react';
import { UserProfile } from '@/types';
import { calculateQuitDays, calculateSavedMoney, calculateHealthImprovements } from '@/utils/date';

export function useQuitStats(userProfile: UserProfile | null) {
  return useMemo(() => {
    if (!userProfile || !userProfile.quit_date) {
      return {
        quitDays: 0,
        savedMoney: 0,
        extendedLife: 0,
        healthImprovements: [],
        badges: [],
      };
    }

    const quitDays = calculateQuitDays(userProfile.quit_date);
    const savedMoney = calculateSavedMoney(
      userProfile.daily_cigarettes || 0,
      userProfile.pack_cost || 0,
      userProfile.quit_date
    );
    const extendedLife = quitDays * 0.1; // 1日0.1時間延長
    const healthImprovements = calculateHealthImprovements(userProfile.quit_date);

    return {
      quitDays,
      savedMoney,
      extendedLife,
      healthImprovements,
      badges: userProfile.badges || [],
    };
  }, [userProfile]);
}
