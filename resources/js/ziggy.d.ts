// resources/js/ziggy.d.ts
import { route as ziggyRoute } from 'ziggy-js';

declare global {
    var route: typeof ziggyRoute;
}

export {};
