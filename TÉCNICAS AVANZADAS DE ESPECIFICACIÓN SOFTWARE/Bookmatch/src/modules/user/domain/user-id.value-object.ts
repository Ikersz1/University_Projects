import {
    v4 as uuidv4,
    //validate as uuidValidate
} from "uuid";

class UserId {
    private readonly value: string;

    public constructor(id: string) {
        //this.guard(id);

        this.value = id;
    }

    public static random(): UserId {
        return new UserId(uuidv4());
    }

    // private guard(id: string) {
    //     if (false === uuidValidate(id)) {
    //         throw new Error(`Invalid User ID ${id}`);
    //     }
    // }

    public getValue(): string {
        return this.value;
    }

    public equals(other: UserId): boolean {
        return this.value === other.value;
    }
}

export default UserId;
