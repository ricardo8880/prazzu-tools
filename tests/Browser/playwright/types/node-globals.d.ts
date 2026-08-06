declare namespace NodeJS {
    interface ProcessEnv {
        [key: string]: string | undefined;
    }
}

declare const process: {
    env: NodeJS.ProcessEnv;
    cwd(): string;
    platform: string;
};

declare class Buffer extends Uint8Array {
    static from(value: string | ArrayBuffer | ArrayLike<number>, encoding?: string): Buffer;
    static alloc(size: number): Buffer;
    toString(encoding?: string): string;
    readUInt32LE(offset: number): number;
    subarray(begin?: number, end?: number): Buffer;
}

declare module 'node:fs' {
    export const mkdirSync: (...args: any[]) => any;
    export const writeFileSync: (...args: any[]) => any;
    export const readFileSync: (...args: any[]) => any;
    export const existsSync: (...args: any[]) => boolean;
    const fs: any;
    export default fs;
}

declare module 'node:path' {
    export const resolve: (...parts: string[]) => string;
    export const dirname: (path: string) => string;
    export const basename: (path: string) => string;
    export const extname: (path: string) => string;
    export const relative: (from: string, to: string) => string;
    const path: any;
    export default path;
}

declare module 'node:child_process' {
    export const execFileSync: (...args: any[]) => any;
}

declare module 'node:crypto' {
    export const randomUUID: () => string;
}
